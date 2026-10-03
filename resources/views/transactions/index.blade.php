<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử giao dịch | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-black text-white antialiased">
    @include('partials.site-header')
    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">&larr; Quay lại sự kiện</a>
        <div class="mb-8 mt-4">
            <h1 class="text-3xl font-bold">Lịch sử giao dịch</h1>
            <p class="mt-2 text-sm text-neutral-400">Theo dõi vé đã đặt, thanh toán và đơn đã hủy.</p>
        </div>

        @if ($orders->isEmpty())
            <div class="w-full rounded-2xl border border-dashed border-white/15 px-5 py-14 text-center text-neutral-400">
                <p class="text-lg font-semibold text-white">Bạn chưa có giao dịch nào</p>
                <p class="mt-2 text-sm">Các giao dịch sẽ xuất hiện tại đây sau khi bạn đặt vé.</p>
                <a href="{{ route('dashboard') }}" class="mt-5 inline-flex rounded-full bg-violet-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-violet-500">Khám phá sự kiện</a>
            </div>
        @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($orders as $order)
                @php
                    $eventNames = $order->items->pluck('event_title')->filter()->unique()->values();
                    $event = $order->items->first()?->ticketType?->event;
                    $statusStyles = ['pending' => 'border-amber-300/15 bg-amber-300/[.04]', 'confirmed' => 'border-emerald-300/15 bg-emerald-300/[.04]', 'cancelled' => 'border-rose-300/15 bg-rose-300/[.04]'];
                    $statusLabels = ['pending' => 'Chưa thanh toán', 'confirmed' => 'Đã thanh toán', 'cancelled' => 'Đã hủy'];
                    $statusText = ['pending' => 'text-amber-200 bg-amber-300/10', 'confirmed' => 'text-emerald-200 bg-emerald-300/10', 'cancelled' => 'text-rose-200 bg-rose-300/10'];
                @endphp
                <article class="overflow-hidden rounded-2xl border {{ $statusStyles[$order->status] ?? 'border-white/10 bg-neutral-900' }} shadow-lg shadow-black/20">
                    <div class="relative h-48 bg-neutral-800">
                        @if ($event?->cover_image)
                            <img src="{{ asset($event->cover_image) }}" alt="{{ $eventNames->join(', ') ?: 'Vé sự kiện' }}" class="h-full w-full object-cover">
                        @else
                            <div class="grid h-full place-items-center text-neutral-500">Chưa có hình sự kiện</div>
                        @endif
                        <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-xs font-bold {{ $statusText[$order->status] ?? 'bg-white/10 text-neutral-300' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                    </div>
                    <div class="p-5">
                        <div class="flex min-h-16 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="line-clamp-2 text-lg font-bold">{{ $eventNames->isNotEmpty() ? $eventNames->join(', ') : 'Vé sự kiện' }}</h2>
                                <p class="mt-1 text-xs text-neutral-400">{{ $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p>
                            </div>
                            <p class="shrink-0 text-right font-bold text-violet-300">{{ number_format($order->total, 0, ',', '.') }} ₫</p>
                        </div>
                        <div class="mt-3 space-y-1 border-t border-white/10 pt-3 text-sm text-neutral-300">
                            @foreach ($order->items as $item)
                                <p>{{ $item->ticket_name }} × {{ $item->quantity }}</p>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-neutral-500">Mã đơn: {{ $order->code }}</p>
                        @if ($order->status === 'pending')
                            <form method="POST" action="{{ route('payment.vnpay.start', $order) }}" class="mt-5">
                                @csrf
                                <button type="submit" class="w-full rounded-full bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-violet-500">Thanh toán qua VNPay</button>
                            </form>
                        @elseif ($order->status === 'cancelled' && $event)
                            @php
                                $ticketsToRepurchase = $order->items
                                    ->filter(fn ($item) => $item->ticketType && $item->ticketType->event_id === $event->id)
                                    ->mapWithKeys(fn ($item) => [$item->ticket_type_id => $item->quantity])
                                    ->all();
                            @endphp
                            @if ($ticketsToRepurchase)
                                <a href="{{ route('ticket-detail', ['event' => $event->slug, 'tickets' => $ticketsToRepurchase]) }}" class="mt-5 flex w-full items-center justify-center rounded-full bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-violet-500">Mua lại</a>
                            @endif
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        @endif
        <div class="mt-6">{{ $orders->links() }}</div>
    </main>
</body>
</html>
