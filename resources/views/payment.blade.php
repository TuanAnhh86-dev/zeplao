<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán {{ $order->code }} | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-black text-white antialiased">
    @include('partials.site-header')
    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">← Vé của tôi</a>
        <section class="mt-5 rounded-3xl border border-white/10 bg-neutral-900 p-6 sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[.16em] text-violet-300">Thanh toán</p>
            <h1 class="mt-2 text-2xl font-bold">Đơn {{ $order->code }}</h1>
            @if (session('status'))
                <p class="mt-4 rounded-xl border border-white/10 bg-white/5 p-3 text-sm text-neutral-200" role="status">{{ session('status') }}</p>
            @endif
            <div class="mt-5 divide-y divide-white/10 border-y border-white/10">
                @foreach ($order->items as $item)
                    <div class="flex justify-between gap-4 py-3 text-sm"><span>{{ $item->ticket_name }} × {{ $item->quantity }}</span><span>{{ number_format($item->subtotal, 0, ',', '.') }} ₫</span></div>
                @endforeach
                <div class="flex justify-between gap-4 py-4 font-bold"><span>Tổng thanh toán</span><span class="text-violet-300">{{ number_format($order->total, 0, ',', '.') }} ₫</span></div>
            </div>

            @if ($order->paymentTransactions->isNotEmpty())
                <section class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4" aria-label="Lịch sử giao dịch VNPay">
                    <h2 class="font-semibold">Lịch sử giao dịch VNPay</h2>
                    <div class="mt-3 space-y-3">
                        @foreach ($order->paymentTransactions as $transaction)
                            <div class="rounded-xl border border-white/10 p-3 text-sm">
                                <p>{{ $transaction->callback_type === 'ipn' ? 'IPN' : 'Return URL' }} · {{ $transaction->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}</p>
                                <p class="mt-1 text-neutral-300">Mã giao dịch: {{ $transaction->transaction_no ?? '—' }} · Mã phản hồi: {{ $transaction->response_code ?? '—' }} · Trạng thái: {{ $transaction->transaction_status ?? '—' }}</p>
                                <p class="mt-1 text-neutral-400">Chữ ký {{ $transaction->signature_valid ? 'hợp lệ' : 'không hợp lệ' }}{{ $transaction->bank_code ? ' · Ngân hàng: '.$transaction->bank_code : '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($order->status === 'pending')
                <div class="mt-6 rounded-2xl border border-amber-300/20 bg-amber-300/10 p-4" role="status">
                    <h2 class="font-semibold text-amber-100">Đơn được giữ vé trong tối đa 10 phút</h2>
                    <p class="mt-2 text-sm leading-6 text-amber-100/80">Hoàn tất thanh toán trước {{ $order->expires_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i:s d/m/Y') }}. Đơn sẽ tự hủy khi hết hạn.</p>
                </div>
                <div class="mt-5 flex flex-wrap justify-between gap-3">
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="flex flex-wrap gap-2">@csrf<button class="rounded-full border border-rose-400/30 px-5 py-3 text-sm font-semibold text-rose-200 hover:bg-rose-400/10">Hủy đơn chưa thanh toán</button></form>
                    @if ($vnpayConfigured)
                        <form method="POST" action="{{ route('payment.vnpay.start', $order) }}">@csrf<button class="rounded-full bg-violet-600 px-5 py-3 text-sm font-semibold hover:bg-violet-500">Thanh toán qua VNPay Sandbox</button></form>
                    @else
                        <p class="self-center text-sm text-rose-200">Chưa cấu hình VNPAY_TMN_CODE và VNPAY_HASH_SECRET trong .env.</p>
                    @endif
                </div>
            @elseif ($order->status === 'confirmed')
                <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 p-4"><h2 class="font-semibold text-emerald-100">Đơn đã thanh toán</h2><p class="mt-2 text-sm text-emerald-100/80">Vé đã được xác nhận.</p></div>
            @else
                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4"><h2 class="font-semibold">Đơn đã hủy</h2><p class="mt-2 text-sm text-neutral-300">Đơn chưa thanh toán này đã bị hủy.</p></div>
            @endif
        </section>
    </main>
</body>
</html>
