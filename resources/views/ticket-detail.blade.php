<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ch&#7885;n v&#233; | {{ $event->title }} | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-black text-white antialiased">
    @include('partials.site-header')

    <main class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-center justify-between gap-4">
            <a href="{{ route('select-ticket', ['event' => $event->slug]) }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-violet-300 transition hover:border-violet-400 hover:bg-violet-500/10">&larr; Tr&#7903; v&#7873;</a>
            <div class="text-right"><p class="font-bold text-violet-300">Ch&#7885;n v&#233;</p><p class="text-xs text-neutral-400">{{ $event->ticketTypes->count() }} h&#7841;ng v&#233;</p></div>
        </div>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_390px]">
            <section class="overflow-hidden rounded-3xl border border-white/10 bg-neutral-950" aria-label="S&#417; &#273;&#7891; v&#224; h&#7841;ng v&#233;">
                @if ($seatMapImage)
                    <div class="flex min-h-[460px] items-center justify-center bg-black p-4 sm:min-h-[620px] sm:p-8">
                        <img src="{{ asset($seatMapImage) }}" alt="S&#417; &#273;&#7891; khu v&#7921;c {{ $event->title }}" class="max-h-[78vh] w-full object-contain" />
                    </div>
                    <div class="border-t border-white/10 px-5 py-4 text-center text-sm text-neutral-400">S&#417; &#273;&#7891; khu v&#7921;c s&#7917; d&#7909;ng &#273;&#7875; tham kh&#7843;o h&#7841;ng v&#233;. Ch&#7885;n s&#7889; l&#432;&#7907;ng t&#7915; danh s&#225;ch b&#234;n c&#7841;nh.</div>
                @else
                    <div class="flex min-h-[460px] flex-col justify-center bg-gradient-to-br from-neutral-900 via-neutral-950 to-violet-950/30 p-5 sm:p-10">
                        <div class="mx-auto w-full max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-violet-300">{{ $event->city }}</p>
                            <h1 class="mt-3 text-3xl font-bold leading-tight sm:text-4xl">{{ $event->title }}</h1>
                            <p class="mt-4 text-neutral-300">Kh&#244;ng c&#243; s&#417; &#273;&#7891; ch&#7895; ng&#7891;i cho s&#7921; ki&#7879;n n&#224;y. Ch&#7885;n h&#7841;ng v&#233; v&#224; s&#7889; l&#432;&#7907;ng &#7903; danh s&#225;ch b&#234;n c&#7841;nh.</p>
                            <div class="mt-8 grid gap-3 sm:grid-cols-2"><div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-wide text-neutral-400">Th&#7901;i gian</p><p class="mt-1 font-semibold">{{ $event->starts_at->format('H:i, d/m/Y') }}</p></div><div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-wide text-neutral-400">&#272;&#7883;a &#273;i&#7875;m</p><p class="mt-1 font-semibold">{{ $event->venue }}</p></div></div>
                        </div>
                    </div>
                @endif
            </section>

            <aside class="flex max-h-[calc(100vh-7rem)] flex-col overflow-hidden rounded-3xl border border-white/10 bg-neutral-900 xl:sticky xl:top-24" aria-label="Ch&#7885;n h&#7841;ng v&#233;">
                <div class="border-b border-white/10 bg-neutral-800 p-5">
                    <h1 class="text-lg font-bold leading-snug">{{ $event->title }}</h1>
                    <p class="mt-3 text-sm font-semibold text-violet-300">{{ $event->starts_at->format('H:i, d/m/Y') }}</p>
                    <p class="mt-1 text-sm text-neutral-300">{{ $event->venue }} &middot; {{ $event->city }}</p>
                </div>

                <div class="flex-1 space-y-2 overflow-y-auto p-4" data-ticket-list>
                    <h2 class="mb-3 px-1 text-sm font-bold uppercase tracking-wide text-neutral-300">H&#7841;ng v&#233; v&#224; gi&#225;</h2>
                    @forelse ($event->ticketTypes->sortBy('price') as $ticketType)
                        <article class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-neutral-800/80 p-3" data-ticket-row data-price="{{ $ticketType->price }}">
                            <div class="min-w-0"><h3 class="font-semibold leading-5">{{ $ticketType->name }}</h3><p class="mt-1 text-sm font-bold text-violet-300">{{ number_format($ticketType->price, 0, ',', '.') }} &#273;</p></div>
                            <div class="flex shrink-0 items-center gap-1.5 rounded-xl border border-white/10 bg-neutral-950 p-1">
                                <button type="button" data-ticket-step="-1" aria-label="Gi&#7843;m s&#7889; l&#432;&#7907;ng" class="grid size-8 place-items-center rounded-lg text-neutral-300 transition hover:bg-white/10 disabled:opacity-40" disabled>&minus;</button>
                                <output data-ticket-quantity class="min-w-7 text-center text-sm font-semibold tabular-nums">0</output>
                                <button type="button" data-ticket-step="1" aria-label="T&#259;ng s&#7889; l&#432;&#7907;ng" class="grid size-8 place-items-center rounded-lg bg-violet-600 text-white transition hover:bg-violet-500">+</button>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-white/15 p-5 text-sm text-neutral-400">Ch&#432;a c&#243; h&#7841;ng v&#233; cho s&#7921; ki&#7879;n n&#224;y.</p>
                    @endforelse
                </div>

                <div class="border-t border-white/10 bg-neutral-950 p-4 sm:p-5">
                    <div class="mb-4 flex items-center justify-between gap-3"><div><p class="font-semibold"><span data-ticket-total-count>0</span> v&#233; &#273;&#227; ch&#7885;n</p><p class="mt-1 text-xs text-neutral-400">T&#7893;ng t&#7841;m t&#237;nh</p></div><p class="text-lg font-bold text-violet-300"><span data-ticket-total-price>0</span> &#273;</p></div>
                    <button type="button" data-ticket-checkout class="w-full rounded-full bg-violet-600 px-5 py-3.5 font-bold text-white transition hover:bg-violet-500 disabled:cursor-not-allowed disabled:bg-neutral-700 disabled:text-neutral-400" disabled>Thanh to&#225;n</button>
                    <p data-ticket-message class="mt-3 hidden rounded-xl bg-amber-400/10 px-3 py-2 text-center text-sm text-amber-200">Vui l&#242;ng ch&#7885;n &#237;t nh&#7845;t m&#7897;t v&#233;.</p>
                    <p class="mt-3 text-center text-xs leading-5 text-neutral-500">Ch&#7885;n s&#7889; l&#432;&#7907;ng &#273;&#7875; xem t&#7893;ng ti&#7873;n v&#233;.</p>
                </div>
            </aside>
        </div>
    </main>

    <script>
        const ticketList = document.querySelector('[data-ticket-list]');
        if (ticketList) {
            const totalCount = document.querySelector('[data-ticket-total-count]');
            const totalPrice = document.querySelector('[data-ticket-total-price]');
            const checkout = document.querySelector('[data-ticket-checkout]');
            const message = document.querySelector('[data-ticket-message]');
            const formatPrice = (amount) => new Intl.NumberFormat('vi-VN').format(amount);
            const updateTotals = () => {
                let count = 0;
                let price = 0;
                ticketList.querySelectorAll('[data-ticket-row]').forEach((row) => {
                    const quantity = Number(row.querySelector('[data-ticket-quantity]').textContent);
                    count += quantity;
                    price += quantity * Number(row.dataset.price);
                });
                totalCount.textContent = count;
                totalPrice.textContent = formatPrice(price);
                checkout.disabled = count === 0;
                message.classList.add('hidden');
            };
            ticketList.addEventListener('click', (event) => {
                const button = event.target.closest('[data-ticket-step]');
                if (!button) return;
                const row = button.closest('[data-ticket-row]');
                const output = row.querySelector('[data-ticket-quantity]');
                const quantity = Math.max(0, Math.min(10, Number(output.textContent) + Number(button.dataset.ticketStep)));
                output.textContent = quantity;
                row.querySelector('[data-ticket-step="-1"]').disabled = quantity === 0;
                updateTotals();
            });
            checkout.addEventListener('click', () => {
                message.textContent = `Đã chọn ${totalCount.textContent} vé, tổng ${totalPrice.textContent} đ. Chức năng thanh toán chưa được kết nối.`;
                message.classList.remove('hidden');
            });
        }
    </script>
</body>
</html>
