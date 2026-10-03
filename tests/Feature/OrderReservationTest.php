<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketType;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_pending_order_reserves_stock_and_retries_are_idempotent(): void
    {
        [$event, $ticket] = $this->eventWithTicket(1);
        $user = User::factory()->create();
        $key = (string) Str::uuid();

        $first = $this->actingAs($user)->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => $key,
        ])->assertOk();
        $this->assertSame('Đơn đã tạo. Vui lòng tiếp tục đến trang thanh toán.', $first->json('message'));
        $order = Order::where('code', $first->json('code'))->firstOrFail();
        $this->assertSame(600, $order->expires_at->timestamp - $order->created_at->timestamp);

        $retry = $this->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => $key,
        ])->assertOk();
        $this->assertSame($first->json('code'), $retry->json('code'));

        $this->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
        ])->assertStatus(409);
        $this->assertSame(0, $ticket->fresh()->sold);
        $this->assertSame(1, Order::where('status', 'pending')->count());
    }

    public function test_expired_hold_is_released_and_cannot_be_confirmed(): void
    {
        [$event, $ticket] = $this->eventWithTicket(1);
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
        ])->assertOk();
        $order = Order::where('code', $response->json('code'))->firstOrFail();

        $this->travel(11)->minutes();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'confirmed'])
            ->assertStatus(422);

        $this->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
        ])->assertOk();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(0, $ticket->fresh()->sold);
    }

    public function test_confirmation_converts_hold_to_sold_once_and_cancellation_releases_hold(): void
    {
        [$event, $ticket] = $this->eventWithTicket(2);
        $user = User::factory()->create();
        $orders = [];
        foreach (range(1, 2) as $index) {
            $response = $this->actingAs($user)->postJson(route('orders.store', $event), [
                'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
            ])->assertOk();
            $orders[] = Order::where('code', $response->json('code'))->firstOrFail();
        }

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->patch(route('admin.orders.update', $orders[0]), ['status' => 'confirmed'])->assertSessionHasNoErrors();
        $this->assertSame(1, $ticket->fresh()->sold);
        $this->patch(route('admin.orders.update', $orders[1]), ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertSame(1, $ticket->fresh()->sold);
        $this->assertSame('cancelled', $orders[1]->fresh()->status);
        $this->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
        ])->assertOk();
    }

    public function test_admin_cannot_reduce_capacity_below_active_holds(): void
    {
        [$event, $ticket] = $this->eventWithTicket(1);
        $customer = User::factory()->create();
        $this->actingAs($customer)->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
        ])->assertOk();

        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'title' => $event->title, 'slug' => $event->slug, 'category' => $event->category,
            'city' => $event->city, 'city_key' => $event->city_key, 'venue' => $event->venue,
            'starts_at' => $event->starts_at->format('Y-m-d H:i:s'), 'cover_image' => $event->cover_image,
            'ticket_types' => [['id' => $ticket->id, 'name' => $ticket->name, 'price' => $ticket->price, 'quantity' => 0]],
        ];

        $this->actingAs($admin)->put(route('admin.events.update', $event), $payload)->assertStatus(422);
        $this->assertSame(1, $ticket->fresh()->quantity);

        $payload['ticket_types'] = [['name' => 'Extra', 'price' => 200, 'quantity' => 3]];
        $this->put(route('admin.events.update', $event), $payload)->assertRedirect(route('admin.events'));
        $this->assertDatabaseHas('ticket_types', ['id' => $ticket->id, 'quantity' => 1]);
    }

    public function test_admin_event_listing_subtracts_active_holds_from_available_tickets(): void
    {
        [$event, $ticket] = $this->eventWithTicket(3);
        $customer = User::factory()->create();
        $order = Order::create([
            'code' => 'TX-HELD001', 'user_id' => $customer->id, 'total' => 100,
            'status' => 'pending', 'expires_at' => now()->addMinutes(10),
        ]);
        $order->items()->create([
            'ticket_type_id' => $ticket->id,
            'ticket_name' => $ticket->name,
            'event_title' => $event->title,
            'unit_price' => $ticket->price,
            'quantity' => 1,
            'subtotal' => $ticket->price,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->withoutVite()->actingAs($admin)->get(route('admin.events'))
            ->assertOk()
            ->assertSee('còn 2/3');
    }

    public function test_admin_event_date_filter_requires_either_date_or_month(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->from(route('admin.events'))
            ->actingAs($admin)
            ->get(route('admin.events', ['date' => '2026-10-03', 'month' => '2026-10']))
            ->assertRedirect(route('admin.events'))
            ->assertSessionHasErrors('date');
    }

    public function test_home_city_filter_options_include_custom_admin_city_keys(): void
    {
        [$event] = $this->eventWithTicket(3);
        $event->update(['city' => 'Cần Thơ', 'city_key' => 'can-tho']);
        $customer = User::factory()->create();

        $this->withoutVite()->actingAs($customer)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('value="can-tho"', false)
            ->assertSee('Cần Thơ');
    }

    public function test_ticket_detail_displays_available_stock_after_active_holds(): void
    {
        [$event, $ticket] = $this->eventWithTicket(1);
        $customer = User::factory()->create();
        $this->actingAs($customer)->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1], 'idempotency_key' => (string) Str::uuid(),
        ])->assertOk();

        $this->withoutVite()->get(route('ticket-detail', $event))
            ->assertOk()->assertSee('data-available="0"', false);
    }

    public function test_my_tickets_page_shows_only_the_authenticated_users_orders(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $order = Order::create([
            'code' => 'TX-CUSTOM01', 'user_id' => $customer->id, 'total' => 600, 'status' => 'confirmed',
        ]);
        $customerItem = $order->items()->create([
            'ticket_name' => 'General', 'event_title' => 'Customer event',
            'unit_price' => 600, 'quantity' => 1, 'subtotal' => 600,
        ]);
        $customerItem->qrInfo()->create(['token' => (string) Str::uuid(), 'status' => 'unused']);
        $usedItem = $order->items()->create([
            'ticket_name' => 'VIP', 'event_title' => 'Customer event',
            'unit_price' => 900, 'quantity' => 1, 'subtotal' => 900,
        ]);
        $usedItem->qrInfo()->create(['token' => (string) Str::uuid(), 'status' => 'used']);
        $otherOrder = Order::create([
            'code' => 'TX-OTHER001', 'user_id' => $otherCustomer->id, 'total' => 900, 'status' => 'confirmed',
        ]);
        $otherOrder->items()->create([
            'ticket_name' => 'VIP', 'event_title' => 'Private event',
            'unit_price' => 900, 'quantity' => 1, 'subtotal' => 900,
        ]);

        $this->withoutVite()->actingAs($customer)->get(route('my-tickets'))
            ->assertOk()
            ->assertSee('Vé của tôi')
            ->assertSee('Chưa sử dụng')
            ->assertSee('bg-neutral-200')
            ->assertSee('Đã sử dụng')
            ->assertSee('bg-emerald-400')
            ->assertSee('Customer event')
            ->assertDontSee('Private event');
    }

    public function test_transactions_offer_vnpay_for_pending_orders_and_prefill_cancelled_tickets_for_repurchase(): void
    {
        [$event, $ticket] = $this->eventWithTicket(5);
        $customer = User::factory()->create();

        $pendingOrder = Order::create([
            'code' => 'TX-PENDING01', 'user_id' => $customer->id, 'total' => 600,
            'status' => 'pending', 'expires_at' => now()->addMinutes(10),
        ]);
        $pendingOrder->items()->create([
            'ticket_type_id' => $ticket->id, 'ticket_name' => $ticket->name,
            'event_title' => $event->title, 'unit_price' => 600, 'quantity' => 1, 'subtotal' => 600,
        ]);

        $cancelledOrder = Order::create([
            'code' => 'TX-CANCEL001', 'user_id' => $customer->id, 'total' => 1200, 'status' => 'cancelled',
        ]);
        $cancelledOrder->items()->create([
            'ticket_type_id' => $ticket->id, 'ticket_name' => $ticket->name,
            'event_title' => $event->title, 'unit_price' => 600, 'quantity' => 2, 'subtotal' => 1200,
        ]);

        $repurchaseUrl = route('ticket-detail', [
            'event' => $event->slug,
            'tickets' => [$ticket->id => 2],
        ]);

        $this->withoutVite()->actingAs($customer)->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Thanh toán qua VNPay')
            ->assertSee(route('payment.vnpay.start', $pendingOrder), false)
            ->assertSee('Mua lại')
            ->assertSee($repurchaseUrl, false);

        $this->get($repurchaseUrl)
            ->assertOk()
            ->assertSee('data-prefill-quantity="2"', false);
    }

    public function test_event_seeder_initializes_stock_to_ten_and_preserves_existing_stock_and_order_history(): void
    {
        (new EventSeeder())->run();
        $this->assertGreaterThan(0, TicketType::count());
        $this->assertSame(0, TicketType::where('quantity', '!=', 10)->count());

        $ticket = TicketType::firstOrFail();
        $ticket->update(['quantity' => 23, 'sold' => 5]);
        $obsolete = $ticket->event->ticketTypes()->create([
            'name' => 'Legacy ticket', 'price' => 100, 'quantity' => 10, 'sold' => 0,
        ]);
        $user = User::factory()->create();
        $order = Order::create([
            'code' => 'TX-LEGACY01', 'user_id' => $user->id, 'total' => 100,
            'status' => 'confirmed',
        ]);
        $order->items()->create([
            'ticket_type_id' => $obsolete->id, 'ticket_name' => $obsolete->name,
            'unit_price' => 100, 'quantity' => 1, 'subtotal' => 100,
        ]);

        (new EventSeeder())->run();

        $this->assertSame(23, $ticket->fresh()->quantity);
        $this->assertSame(5, $ticket->fresh()->sold);
        $this->assertDatabaseHas('ticket_types', ['id' => $obsolete->id]);
        $this->assertDatabaseHas('order_items', ['ticket_type_id' => $obsolete->id]);
    }

    private function eventWithTicket(int $quantity): array
    {
        $event = Event::create([
            'title' => 'Test event', 'slug' => 'test-event', 'category' => 'music',
            'city' => 'Hanoi', 'city_key' => 'hanoi', 'venue' => 'Test venue',
            'starts_at' => now()->addDay(), 'cover_image' => 'test.jpg',
        ]);
        $ticket = $event->ticketTypes()->create(['name' => 'General', 'price' => 100, 'quantity' => $quantity, 'sold' => 0]);
        return [$event, $ticket];
    }
}
