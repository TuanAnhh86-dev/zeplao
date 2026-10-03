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

        @if ($orders->isEmpty())
            <div class="w-full rounded-2xl border border-dashed border-white/15 px-5 py-14 text-center text-neutral-400">
                <p class="text-lg font-semibold text-white">Bạn chưa có vé đã thanh toán</p>
                <p class="mt-2 text-sm">Các vé sẽ xuất hiện tại đây sau khi thanh toán thành công.</p>
                <a href="{{ route('dashboard') }}" class="mt-5 inline-flex rounded-full bg-violet-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-violet-500">Khám phá sự kiện</a>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($orders as $order)
                @foreach ($order->items as $item)
                    @php($event = $item->ticketType?->event)
                    @php($usedQrCount = $item->qrInfo->where('status', 'used')->count())
                    <article class="overflow-hidden rounded-2xl border border-white/10 bg-neutral-900 shadow-lg shadow-black/20">
                        <div class="relative h-48 bg-neutral-800">
                            @if ($event?->cover_image)
                                <img src="{{ asset($event->cover_image) }}" alt="{{ $item->event_title }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full place-items-center text-neutral-500">Chưa có hình sự kiện</div>
                            @endif
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                @if ($usedQrCount === 0)
                                    <span class="rounded-full border border-neutral-300 bg-neutral-200 px-3 py-1 text-xs font-bold text-neutral-800 shadow-sm">Chưa sử dụng</span>
                                @elseif ($usedQrCount === $item->qrInfo->count())
                                    <span class="rounded-full border border-emerald-300 bg-emerald-400 px-3 py-1 text-xs font-bold text-emerald-950 shadow-sm">Đã sử dụng</span>
                                @else
                                    <span class="rounded-full border border-emerald-300 bg-emerald-400 px-3 py-1 text-xs font-bold text-emerald-950 shadow-sm">Đã sử dụng {{ $usedQrCount }}</span>
                                    <span class="rounded-full border border-neutral-300 bg-neutral-200 px-3 py-1 text-xs font-bold text-neutral-800 shadow-sm">Chưa sử dụng {{ $item->qrInfo->count() - $usedQrCount }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-5">
                            <h2 class="line-clamp-2 min-h-12 text-lg font-bold">{{ $item->event_title ?: 'Vé sự kiện' }}</h2>
                            <p class="mt-2 text-sm text-neutral-400">{{ $item->ticket_name }} <span class="text-neutral-600">·</span> {{ $item->quantity }} vé</p>
                            <p class="mt-1 text-xs text-neutral-500">Mã đơn: {{ $order->code }}</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <button type="button" onclick="document.getElementById('ticket-details-{{ $item->id }}').showModal()" class="flex-1 rounded-full border border-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:border-violet-400 hover:bg-white/5">Thông tin chi tiết</button>
                                <button type="button" onclick="document.getElementById('ticket-qr-{{ $item->id }}').showModal()" class="flex-1 rounded-full bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-violet-500">Show QR</button>
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
                    <dialog id="ticket-qr-{{ $item->id }}" class="fixed inset-0 m-0 h-screen w-screen max-h-none max-w-none overflow-hidden border-0 bg-white p-0 backdrop:bg-black/90">
                        <div class="relative grid h-full w-full place-items-center">
                            <button type="button" onclick="this.closest('dialog').close()" aria-label="Đóng mã QR" class="absolute right-4 top-4 z-10 grid size-10 place-items-center rounded-full text-3xl text-neutral-700 hover:bg-neutral-100">&times;</button>
                            @if ($item->qrInfo->isNotEmpty())
                                @if ($item->qrInfo->count() > 1)
                                    <button type="button" data-qr-previous aria-label="Mã QR trước" class="absolute left-2 top-1/2 z-10 grid size-10 -translate-y-1/2 place-items-center rounded-full text-3xl text-neutral-500 hover:bg-neutral-100 sm:left-6">&lsaquo;</button>
                                    <button type="button" data-qr-next aria-label="Mã QR tiếp theo" class="absolute right-2 top-1/2 z-10 grid size-10 -translate-y-1/2 place-items-center rounded-full text-3xl text-neutral-500 hover:bg-neutral-100 sm:right-6">&rsaquo;</button>
                                @endif
                                <div class="w-[min(78vw,440px)]">
                                    @foreach ($item->qrInfo as $qr)
                                        <div data-ticket-qr data-qr-token="{{ $qr->token }}" class="{{ $loop->first ? '' : 'hidden' }}"></div>
                                    @endforeach
                                </div>
                            @else
                                <p class="px-6 text-center text-sm text-rose-700">Vé chưa có mã QR.</p>
                            @endif
                        </div>
                    </dialog>
                @endforeach
                @endforeach
            </div>
        @endif
        <div class="mt-6">{{ $orders->links() }}</div>
    </main>
    <script>
        let screenWakeLock = null;
        const qrDialogs = [...document.querySelectorAll('[id^="ticket-qr-"]')];
        const releaseScreenWakeLock = async () => {
            if (!screenWakeLock) return;
            const lock = screenWakeLock;
            screenWakeLock = null;
            await lock.release();
        };
        const requestScreenWakeLock = async () => {
            if (!('wakeLock' in navigator) || document.visibilityState !== 'visible') return;
            try {
                screenWakeLock = await navigator.wakeLock.request('screen');
            } catch (error) {
                console.warn('Unable to keep the screen awake while displaying a ticket QR code.', error);
            }
        };

        qrDialogs.forEach((dialog) => {
            const qrTargets = [...dialog.querySelectorAll('[data-ticket-qr]')];
            let activeQrIndex = 0;
            const showQr = (index) => {
                if (!qrTargets.length) return;
                activeQrIndex = (index + qrTargets.length) % qrTargets.length;
                qrTargets.forEach((target, targetIndex) => target.classList.toggle('hidden', targetIndex !== activeQrIndex));
            };
            dialog.querySelector('[data-qr-previous]')?.addEventListener('click', () => showQr(activeQrIndex - 1));
            dialog.querySelector('[data-qr-next]')?.addEventListener('click', () => showQr(activeQrIndex + 1));

            dialog.addEventListener('toggle', () => {
                if (!dialog.open) {
                    if (!qrDialogs.some((openDialog) => openDialog.open)) releaseScreenWakeLock();
                    return;
                }

                requestScreenWakeLock();
                showQr(0);
                const unrenderedTargets = qrTargets.filter((target) => !target.hasAttribute('data-rendered'));
                if (!unrenderedTargets.length) return;
                if (!window.QRCode) {
                    unrenderedTargets.forEach((target) => {
                        target.textContent = 'Không thể tải chức năng QR. Vui lòng tải lại trang.';
                        target.classList.add('text-sm', 'text-rose-700');
                    });
                    return;
                }

                unrenderedTargets.forEach((target) => window.QRCode.toCanvas(target.dataset.qrToken, { width: 440, margin: 2, errorCorrectionLevel: 'H' }, (error, canvas) => {
                    if (error) {
                        target.textContent = 'Không thể tạo mã QR.';
                        target.classList.add('text-sm', 'text-rose-700');
                        console.error('Unable to generate a ticket QR code.', error);
                        return;
                    }
                    canvas.className = 'block h-auto w-full';
                    canvas.setAttribute('aria-label', 'Mã QR vé');
                    target.append(canvas);
                    target.dataset.rendered = 'true';
                }));
            });
        });

        document.addEventListener('visibilitychange', () => {
            const openDialog = qrDialogs.find((dialog) => dialog.open);
            if (document.visibilityState === 'visible' && openDialog) requestScreenWakeLock();
            if (document.visibilityState !== 'visible') releaseScreenWakeLock();
        });
    </script>
</body>
</html>
