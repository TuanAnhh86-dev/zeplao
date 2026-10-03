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
    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">&larr; Quay lại sự kiện</a>
        <div class="mb-8 mt-4">
            <h1 class="mt-2 text-3xl font-bold">Vé của tôi</h1>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($orders as $order)
                @foreach ($order->items as $item)
                    @php($event = $item->ticketType?->event)
                    <article class="overflow-hidden rounded-2xl border border-white/10 bg-neutral-900 shadow-lg shadow-black/20">
                        <div class="relative h-48 bg-neutral-800">
                            @if ($event?->cover_image)
                                <img src="{{ asset($event->cover_image) }}" alt="{{ $item->event_title }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full place-items-center text-neutral-500">Chưa có hình sự kiện</div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-full bg-emerald-500/90 px-3 py-1 text-xs font-bold text-white">Đã thanh toán</span>
                        </div>
                        <div class="p-5">
                            <h2 class="line-clamp-2 min-h-12 text-lg font-bold">{{ $item->event_title ?: 'Vé sự kiện' }}</h2>
                            <p class="mt-2 text-sm text-neutral-400">{{ $item->ticket_name }} <span class="text-neutral-600">·</span> {{ $item->quantity }} vé</p>
                            <p class="mt-1 text-xs text-neutral-500">Mã đơn: {{ $order->code }}</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <button type="button" onclick="document.getElementById('ticket-details-{{ $item->id }}').showModal()" class="flex-1 rounded-full border border-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:border-violet-400 hover:bg-white/5">Thông tin chi tiết</button>
                                <button type="button" class="flex-1 rounded-full bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-violet-500">Show QR</button>
                            </div>
                        </div>
                    </article>
                    <dialog id="ticket-details-{{ $item->id }}" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-white/10 bg-neutral-900 p-0 text-white shadow-2xl backdrop:bg-black/80">
                        <div class="flex items-start justify-between gap-4 border-b border-white/10 p-5">
                            <div><p class="text-xs font-semibold uppercase tracking-widest text-violet-300">Thông tin vé</p><h2 class="mt-2 text-xl font-bold">{{ $item->event_title ?: 'Vé sự kiện' }}</h2></div>
                            <button type="button" onclick="this.closest('dialog').close()" aria-label="Đóng" class="grid size-9 shrink-0 place-items-center rounded-full text-xl text-neutral-400 hover:bg-white/10 hover:text-white">&times;</button>
                        </div>
                        <dl class="space-y-4 p-5 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-neutral-400">Hạng vé</dt><dd class="text-right font-semibold">{{ $item->ticket_name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-400">Số lượng</dt><dd class="text-right font-semibold">{{ $item->quantity }} vé</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-400">Đơn giá</dt><dd class="text-right font-semibold">{{ number_format($item->unit_price, 0, ',', '.') }} ₫</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-400">Tổng tiền</dt><dd class="text-right font-bold text-violet-300">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-400">Mã đơn</dt><dd class="text-right font-semibold">{{ $order->code }}</dd></div>
                            @if ($event?->starts_at)<div class="flex justify-between gap-4"><dt class="text-neutral-400">Thời gian</dt><dd class="text-right font-semibold">{{ $event->starts_at->timezone('Asia/Ho_Chi_Minh')->format('H:i, d/m/Y') }}</dd></div>@endif
                            @if ($event?->venue)<div class="flex justify-between gap-4"><dt class="text-neutral-400">Địa điểm</dt><dd class="text-right font-semibold">{{ $event->venue }}</dd></div>@endif
                        </dl>
                    </dialog>
                @endforeach
            @empty
                <div class="rounded-2xl border border-dashed border-white/15 px-5 py-14 text-center text-neutral-400 sm:col-span-2 lg:col-span-3">
                    <p class="text-lg font-semibold text-white">Bạn chưa có vé đã thanh toán</p>
                    <p class="mt-2 text-sm">Các vé sẽ xuất hiện tại đây sau khi thanh toán thành công.</p>
                    <a href="{{ route('dashboard') }}" class="mt-5 inline-flex rounded-full bg-violet-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-violet-500">Khám phá sự kiện</a>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </main>
</body>
</html>
