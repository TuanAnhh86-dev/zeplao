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
            <div class="mt-5 divide-y divide-white/10 border-y border-white/10">
                @foreach ($order->items as $item)
                    <div class="flex justify-between gap-4 py-3 text-sm"><span>{{ $item->ticket_name }} × {{ $item->quantity }}</span><span>{{ number_format($item->subtotal, 0, ',', '.') }} ₫</span></div>
                @endforeach
                <div class="flex justify-between gap-4 py-4 font-bold"><span>Tổng thanh toán</span><span class="text-violet-300">{{ number_format($order->total, 0, ',', '.') }} ₫</span></div>
            </div>

            @if ($order->status === 'pending')
                <div class="mt-6 rounded-2xl border border-amber-300/20 bg-amber-300/10 p-4" role="status">
                    <h2 class="font-semibold text-amber-100">Cổng thanh toán chưa được tích hợp</h2>
                    <p class="mt-2 text-sm leading-6 text-amber-100/80">Đơn chưa được thanh toán và vé chưa được giữ. Khi cổng thanh toán được kết nối, bạn sẽ có thể hoàn tất thanh toán tại đây. Tồn kho được kiểm tra lại lúc xác nhận thanh toán.</p>
                </div>
                <div class="mt-5 flex flex-wrap justify-between gap-3">
                    <form method="POST" action="{{ route('orders.cancel', $order) }}">@csrf<button class="rounded-full border border-rose-400/30 px-5 py-3 text-sm font-semibold text-rose-200 hover:bg-rose-400/10">Hủy đơn chưa thanh toán</button></form>
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-semibold hover:bg-violet-500">Quay lại</a>
                </div>
            @elseif ($order->status === 'confirmed')
                <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 p-4"><h2 class="font-semibold text-emerald-100">Đơn đã thanh toán</h2><p class="mt-2 text-sm text-emerald-100/80">Vé đã được xác nhận.</p></div>
                <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-5">@csrf<button class="rounded-full border border-rose-400/30 px-5 py-3 text-sm font-semibold text-rose-200 hover:bg-rose-400/10">Hủy đơn và yêu cầu hoàn tiền</button></form>
            @elseif ($order->status === 'refund_pending')
                <div class="mt-6 rounded-2xl border border-amber-300/20 bg-amber-300/10 p-4"><h2 class="font-semibold text-amber-100">Đang chờ hoàn tiền</h2><p class="mt-2 text-sm text-amber-100/80">Yêu cầu hoàn tiền toàn bộ đơn đã được gửi tới admin.</p></div>
            @elseif ($order->status === 'refunded')
                <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 p-4"><h2 class="font-semibold text-emerald-100">Đã hoàn tiền</h2><p class="mt-2 text-sm text-emerald-100/80">Admin đã ghi nhận hoàn tiền và vé đã được trả về kho.</p></div>
            @else
                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4"><h2 class="font-semibold">Đơn đã hủy</h2><p class="mt-2 text-sm text-neutral-300">Đơn chưa thanh toán này đã bị hủy.</p></div>
            @endif
        </section>
    </main>
</body>
</html>
