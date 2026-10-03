<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use LogicException;

class VnpayService
{
    public function isConfigured(): bool
    {
        return filled(config('services.vnpay.tmn_code'))
            && filled(config('services.vnpay.hash_secret'));
    }

    public function createPaymentUrl(Order $order, Request $request): string
    {
        $this->ensureConfigured();

        $now = now('Asia/Ho_Chi_Minh');
        $data = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => config('services.vnpay.tmn_code'),
            'vnp_Amount' => (string) ($order->total * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $request->ip() ?: '127.0.0.1',
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->code,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => route('payment.vnpay.return'),
            'vnp_TxnRef' => $order->code,
            'vnp_ExpireDate' => $order->expires_at->copy()->timezone('Asia/Ho_Chi_Minh')->format('YmdHis'),
        ];
        ksort($data, SORT_STRING);

        $query = http_build_query($data, '', '&', PHP_QUERY_RFC1738);
        $signature = hash_hmac('sha512', $query, config('services.vnpay.hash_secret'));

        return config('services.vnpay.payment_url').'?'.$query.'&vnp_SecureHash='.$signature;
    }

    public function verifySignature(array $query): bool
    {
        if (! $this->isConfigured() || ! isset($query['vnp_SecureHash']) || ! is_string($query['vnp_SecureHash'])) {
            return false;
        }

        $data = [];
        foreach ($query as $key => $value) {
            if (str_starts_with((string) $key, 'vnp_')
                && ! in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType'], true)
                && is_scalar($value)) {
                $data[$key] = (string) $value;
            }
        }
        ksort($data, SORT_STRING);

        $hashData = http_build_query($data, '', '&', PHP_QUERY_RFC1738);
        $expected = hash_hmac('sha512', $hashData, config('services.vnpay.hash_secret'));

        return hash_equals($expected, $query['vnp_SecureHash']);
    }

    public function matchesOrder(array $query, Order $order): bool
    {
        return is_string($query['vnp_TxnRef'] ?? null)
            && $query['vnp_TxnRef'] === $order->code
            && ($query['vnp_TmnCode'] ?? null) === config('services.vnpay.tmn_code')
            && (! isset($query['vnp_CurrCode']) || $query['vnp_CurrCode'] === 'VND')
            && is_string($query['vnp_Amount'] ?? null)
            && ctype_digit($query['vnp_Amount'])
            && (int) $query['vnp_Amount'] === $order->total * 100;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new LogicException('VNPay Sandbox is not configured. Set VNPAY_TMN_CODE and VNPAY_HASH_SECRET in .env.');
        }
    }
}
