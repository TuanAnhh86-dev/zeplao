<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\QrInfo;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_scan_page_automatically_starts_camera_qr_recognition(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->withoutVite()->actingAs($admin)->get(route('admin.qr-scan'))
            ->assertOk()
            ->assertSee('Camera sẽ tự mở và nhận diện mã QR.')
            ->assertSee('window.Html5Qrcode')
            ->assertSee('tixtak:qr-scanner-ready');
    }

    public function test_admin_can_look_up_a_ticket_qr_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $event = Event::create([
            'title' => 'QR scan test event',
            'slug' => 'qr-scan-test-event',
            'category' => 'music',
            'city' => 'Hanoi',
            'city_key' => 'hanoi',
            'venue' => 'Test venue',
            'starts_at' => now()->addDay(),
            'cover_image' => 'test.jpg',
        ]);
        $ticketType = $event->ticketTypes()->create([
            'name' => 'General',
            'price' => 500,
            'quantity' => 1,
            'sold' => 1,
        ]);
        $order = Order::create([
            'code' => 'TX-QRSCAN01',
            'user_id' => $customer->id,
            'total' => 500,
            'status' => 'confirmed',
        ]);
        $item = $order->items()->create([
            'ticket_type_id' => $ticketType->id,
            'ticket_name' => $ticketType->name,
            'event_title' => $event->title,
            'unit_price' => $ticketType->price,
            'quantity' => 1,
            'subtotal' => $ticketType->price,
        ]);
        $qrInfo = $item->qrInfo()->create([
            'token' => 'c3d762de-b9d7-4cd2-b69e-a223941f5960',
            'status' => 'unused',
        ]);

        $this->withoutVite()->actingAs($admin)->get(route('admin.qr-scan.lookup', ['code' => $qrInfo->token]))
            ->assertOk()
            ->assertSee('Vé hợp lệ')
            ->assertSee($event->title)
            ->assertSee($customer->name)
            ->assertSee('Xác nhận check-in');
    }
}
