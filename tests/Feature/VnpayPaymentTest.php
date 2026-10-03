<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VnpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.vnpay.tmn_code' => 'TESTCODE',
            'services.vnpay.hash_secret' => 'test-secret',
            'services.vnpay.payment_url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
        ]);
    }

    public function test_payment_start_redirects_to_sandbox_with_signed_ngrok_return_url(): void
    {
        $order = $this->pendingOrder();
        $response = $this->actingAs($order->user)->post(route('payment.vnpay.start', $order));
        $response->assertRedirect();

        $url = $response->headers->get('Location');
        $this->assertSame('sandbox.vnpayment.vn', parse_url($url, PHP_URL_HOST));
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame(route('payment.vnpay.return'), $query['vnp_ReturnUrl']);
        $this->assertSame($order->code, $query['vnp_TxnRef']);
        $this->assertSame((string) ($order->total * 100), $query['vnp_Amount']);
        $this->assertTrue(app(\App\Services\VnpayService::class)->verifySignature($query));
    }

    public function test_signed_ipn_confirms_order_and_duplicate_ipn_does_not_double_sell_tickets(): void
    {
        $order = $this->pendingOrder();
        $ticket = TicketType::firstOrFail();
        $query = $this->signedCallback($order);
        $url = route('payment.vnpay.ipn').'?'.http_build_query($query);

        $this->getJson($url)->assertOk()->assertJson(['RspCode' => '00']);
        $this->getJson($url)->assertOk()->assertJson(['RspCode' => '00']);

        $this->assertSame('confirmed', $order->fresh()->status);
        $this->assertSame('VNPAY-TEST-123', $order->fresh()->payment_transaction_id);
        $this->assertSame(1, $ticket->fresh()->sold);
    }

    public function test_ipn_rejects_invalid_signature_and_amount(): void
    {
        $order = $this->pendingOrder();
        $invalidSignature = $this->signedCallback($order);
        $invalidSignature['vnp_SecureHash'] = str_repeat('0', 128);
        $this->getJson(route('payment.vnpay.ipn').'?'.http_build_query($invalidSignature))
            ->assertOk()->assertJson(['RspCode' => '97']);

        $invalidAmount = $this->signedCallback($order);
        $invalidAmount['vnp_Amount'] = (string) (($order->total + 1) * 100);
        $invalidAmount = $this->sign($invalidAmount);
        $this->getJson(route('payment.vnpay.ipn').'?'.http_build_query($invalidAmount))
            ->assertOk()->assertJson(['RspCode' => '04']);

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(0, TicketType::firstOrFail()->sold);
    }

    public function test_late_successful_ipn_does_not_take_tickets_reserved_by_another_order(): void
    {
        $expiredOrder = $this->pendingOrder();
        $ticket = TicketType::firstOrFail();
        $ticket->update(['quantity' => 1]);
        $expiredOrder->update(['status' => 'cancelled']);

        $competingOrder = Order::create([
            'code' => 'TX-COMPETING',
            'user_id' => $expiredOrder->user_id,
            'total' => $expiredOrder->total,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        $competingOrder->items()->create([
            'ticket_type_id' => $ticket->id,
            'ticket_name' => 'General',
            'event_title' => 'VNPay test',
            'unit_price' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        $query = $this->signedCallback($expiredOrder);
        $this->getJson(route('payment.vnpay.ipn').'?'.http_build_query($query))
            ->assertOk()
            ->assertJson(['RspCode' => '04']);

        $this->assertSame('cancelled', $expiredOrder->fresh()->status);
        $this->assertSame(0, $ticket->fresh()->sold);
        $this->assertSame('pending', $competingOrder->fresh()->status);
    }

    private function pendingOrder(): Order
    {
        $event = Event::create([
            'title' => 'VNPay test', 'slug' => 'vnpay-test', 'category' => 'music',
            'city' => 'Hanoi', 'city_key' => 'hanoi', 'venue' => 'Test venue',
            'starts_at' => now()->addDay(), 'cover_image' => 'test.jpg',
        ]);
        $ticket = $event->ticketTypes()->create([
            'name' => 'General', 'price' => 100000, 'quantity' => 2, 'sold' => 0,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('orders.store', $event), [
            'tickets' => [$ticket->id => 1],
            'idempotency_key' => (string) Str::uuid(),
        ])->assertOk();

        return Order::where('code', $response->json('code'))->firstOrFail();
    }

    private function signedCallback(Order $order): array
    {
        return $this->sign([
            'vnp_Amount' => (string) ($order->total * 100),
            'vnp_BankCode' => 'NCB',
            'vnp_CurrCode' => 'VND',
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->code,
            'vnp_ResponseCode' => '00',
            'vnp_TmnCode' => 'TESTCODE',
            'vnp_TransactionNo' => 'VNPAY-TEST-123',
            'vnp_TransactionStatus' => '00',
            'vnp_TxnRef' => $order->code,
            'vnp_Version' => '2.1.0',
        ]);
    }

    private function sign(array $query): array
    {
        unset($query['vnp_SecureHash'], $query['vnp_SecureHashType']);
        ksort($query, SORT_STRING);
        $query['vnp_SecureHash'] = hash_hmac(
            'sha512',
            http_build_query($query, '', '&', PHP_QUERY_RFC1738),
            'test-secret',
        );

        return $query;
    }
}
