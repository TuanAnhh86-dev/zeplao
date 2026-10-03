@extends('admin.layout')

@section('title', 'Quét QR')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[.18em] text-violet-300">Kiểm soát vé</p>
        <h1 class="mt-2 text-3xl font-bold">Quét QR check-in</h1>
        <p class="mt-2 text-sm text-neutral-400">Camera sẽ tự mở và nhận diện mã QR. Bạn cũng có thể nhập mã vé thủ công.</p>
    </div>

    <section class="rounded-2xl border border-white/10 bg-neutral-900/80 p-5 sm:p-7">
        <div id="qr-reader" class="mx-auto hidden max-w-lg overflow-hidden rounded-xl bg-black"></div>
        <div class="flex flex-wrap gap-3">
            <button id="start-scan" type="button" class="rounded-full bg-violet-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-violet-500">Bật camera quét QR</button>
            <button id="stop-scan" type="button" class="hidden rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-neutral-200 hover:bg-white/5">Tắt camera</button>
        </div>
        <p id="scan-message" class="mt-3 text-sm text-neutral-400" role="status" aria-live="polite">Đang khởi động camera quét QR…</p>
        <form method="GET" action="{{ route('admin.qr-scan.lookup') }}" class="mt-5 flex flex-col gap-3 border-t border-white/10 pt-5 sm:flex-row">
            <label class="sr-only" for="ticket-code">Mã vé</label>
            <input id="ticket-code" name="code" value="{{ $scanCode ?? '' }}" required autocomplete="off" placeholder="Nhập mã vé" class="min-w-0 flex-1 rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm text-white placeholder:text-neutral-500 focus:border-violet-400 focus:outline-none">
            <button class="rounded-xl bg-white/10 px-5 py-3 text-sm font-semibold text-white hover:bg-white/15">Tra cứu vé</button>
        </form>
    </section>

    @isset($scanCode)
        @if ($qrInfo)
            @php
                $ticket = $qrInfo->orderItem;
                $order = $ticket?->order;
                $event = $ticket?->ticketType?->event;
                $valid = $order?->status === 'confirmed';
            @endphp
            <section class="mt-5 rounded-2xl border p-5 sm:p-7 {{ $valid ? 'border-violet-300/20 bg-neutral-900/80' : 'border-rose-300/20 bg-rose-300/[.04]' }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div><p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">{{ $valid ? 'Vé hợp lệ' : 'Vé không hợp lệ' }}</p><h2 class="mt-2 text-xl font-bold">{{ $ticket?->event_title ?: 'Vé sự kiện' }}</h2></div>
                    @if ($valid)
                        <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $qrInfo->status === 'unused' ? 'bg-amber-400/10 text-amber-200' : 'bg-neutral-700 text-neutral-300' }}">{{ $qrInfo->status === 'unused' ? 'Chưa sử dụng' : 'Đã sử dụng' }}</span>
                    @else
                        <span class="rounded-full bg-rose-400/10 px-3 py-1.5 text-xs font-bold text-rose-200">Chưa thanh toán</span>
                    @endif
                </div>
                <dl class="mt-5 grid gap-4 border-t border-white/10 pt-5 text-sm sm:grid-cols-2">
                    <div><dt class="text-neutral-500">Khách hàng</dt><dd class="mt-1 font-semibold">{{ $order?->user?->name ?? '—' }}</dd></div>
                    <div><dt class="text-neutral-500">Hạng vé</dt><dd class="mt-1 font-semibold">{{ $ticket?->ticket_name ?? '—' }}</dd></div>
                    <div><dt class="text-neutral-500">Mã đơn</dt><dd class="mt-1 font-semibold">{{ $order?->code ?? '—' }}</dd></div>
                    <div><dt class="text-neutral-500">Thời gian / địa điểm</dt><dd class="mt-1 font-semibold">{{ $event?->starts_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') ?? '—' }}{{ $event?->venue ? ' · '.$event->venue : '' }}</dd></div>
                </dl>
                @if ($valid && $qrInfo->status === 'unused')
                    <form method="POST" action="{{ route('admin.qr-scan.check-in', $qrInfo) }}" class="mt-6">
                        @csrf
                        <button class="w-full rounded-xl bg-violet-600 px-5 py-3.5 font-bold text-white transition hover:bg-violet-500">Xác nhận check-in</button>
                    </form>
                @elseif ($qrInfo->status === 'used')
                    <p class="mt-5 rounded-xl bg-neutral-800 px-4 py-3 text-sm text-neutral-300">Vé đã check-in lúc {{ $qrInfo->used_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') }}.</p>
                @endif
            </section>
        @else
            <section class="mt-5 rounded-2xl border border-rose-300/20 bg-rose-300/[.04] p-5 text-sm text-rose-200">Không tìm thấy mã vé này.</section>
        @endif
    @endisset
</div>

<script>
(() => {
    const start = document.getElementById('start-scan');
    const stop = document.getElementById('stop-scan');
    const reader = document.getElementById('qr-reader');
    const message = document.getElementById('scan-message');
    const input = document.getElementById('ticket-code');
    let scanner;
    let starting = false;
    let submitting = false;

    const waitForScannerLibrary = () => {
        if (window.Html5Qrcode) return Promise.resolve();
        return new Promise((resolve, reject) => {
            const timeout = window.setTimeout(() => {
                window.removeEventListener('tixtak:qr-scanner-ready', onReady);
                reject(new Error('Không tải được chức năng quét QR.'));
            }, 10000);
            const onReady = () => {
                window.clearTimeout(timeout);
                resolve();
            };
            window.addEventListener('tixtak:qr-scanner-ready', onReady, { once: true });
        });
    };

    const stopCamera = async () => {
        if (scanner?.isScanning) await scanner.stop();
        reader.classList.add('hidden');
        stop.classList.add('hidden');
        start.classList.remove('hidden');
    };

    const startCamera = async () => {
        if (starting || scanner?.isScanning) return;
        starting = true;
        start.disabled = true;
        message.textContent = 'Đang yêu cầu quyền camera…';
        try {
            await waitForScannerLibrary();
            if (!window.Html5Qrcode || !navigator.mediaDevices?.getUserMedia) {
                throw new Error('Camera không được hỗ trợ trong trình duyệt này.');
            }
            scanner ??= new window.Html5Qrcode('qr-reader', { verbose: false });
            reader.classList.remove('hidden');
            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 260, height: 260 }, aspectRatio: 1 },
                async (decodedText) => {
                    if (submitting) return;
                    submitting = true;
                    input.value = decodedText.trim();
                    message.textContent = 'Đã nhận diện QR. Đang tra cứu vé…';
                    await stopCamera();
                    input.form.requestSubmit();
                },
                () => {}
            );
            start.classList.add('hidden');
            stop.classList.remove('hidden');
            message.textContent = 'Đưa mã QR vé vào khung camera để tự động nhận diện.';
        } catch (error) {
            await stopCamera();
            if (error instanceof Error) {
                message.textContent = `${error.message} Hãy cấp quyền camera và dùng HTTPS hoặc localhost, hoặc nhập mã vé bên dưới.`;
            } else {
                message.textContent = 'Không mở được camera. Hãy cấp quyền camera và dùng HTTPS hoặc localhost, hoặc nhập mã vé bên dưới.';
                console.error('Unable to start the QR scanner.', error);
            }
        } finally {
            starting = false;
            start.disabled = false;
        }
    };

    stop.addEventListener('click', async () => {
        await stopCamera();
        message.textContent = 'Đã tắt camera.';
    });
    start.addEventListener('click', startCamera);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startCamera, { once: true });
    } else {
        startCamera();
    }
})();
</script>
@endsection
