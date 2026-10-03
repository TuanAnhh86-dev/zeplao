<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé của tôi | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-black text-white antialiased">
    @include('partials.site-header')
    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">← Quay lại sự kiện</a>
        <div class="mb-7 mt-4">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-violet-300">Tài khoản</p>
            <h1 class="mt-2 text-3xl font-bold">Vé của tôi</h1>
            <p class="mt-2 text-sm text-neutral-400">Theo dõi đơn vé, trạng thái thanh toán và vé đã đặt.</p>
        </div>

        <div class="space-y-3">
            @forelse ($orders as $order)
                @php
                    $statusStyles = ['pending' => 'border-amber-300/15 bg-amber-300/[.04]', 'confirmed' => 'border-emerald-300/15 bg-emerald-300/[.04]', 'cancelled' => 'border-rose-300/15 bg-rose-300/[.04]'];
                    $statusLabels = ['pending' => 'Chưa thanh toán', 'confirmed' => 'Đã thanh toán', 'cancelled' => 'Đã hủy'];
                    $statusText = ['pending' => 'text-amber-200 bg-amber-300/10', 'confirmed' => 'text-emerald-200 bg-emerald-300/10', 'cancelled' => 'text-rose-200 bg-rose-300/10'];
                @endphp
                <article class="rounded-2xl border p-5 {{ $statusStyles[$order->status] ?? 'border-white/10 bg-neutral-900' }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold">{{ $order->items->pluck('event_title')->filter()->unique()->join(', ') ?: 'Vé sự kiện' }}</h2>
                            <p class="mt-1 text-xs text-neutral-400">{{ $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-violet-300">{{ number_format($order->total, 0, ',', '.') }} ₫</p>
                            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusText[$order->status] ?? 'bg-white/10 text-neutral-300' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 border-t border-white/10 pt-3 text-sm text-neutral-300">
                        @foreach ($order->items as $item)
                            <span>{{ $item->ticket_name }} × {{ $item->quantity }}</span>
                        @endforeach
                    </div>
                    @if ($order->status === 'pending')
                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <a href="{{ route('payment.show', $order) }}" class="rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold hover:bg-violet-500">Tiếp tục thanh toán</a>
                            <form method="POST" action="{{ route('orders.cancel', $order) }}">
                                @csrf
                                <button class="rounded-full border border-rose-400/30 px-4 py-2 text-sm text-rose-200 hover:bg-rose-400/10">Hủy đơn</button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-white/15 px-5 py-12 text-center text-neutral-400">Bạn chưa có đơn vé nào.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </main>
</body>
</html>
