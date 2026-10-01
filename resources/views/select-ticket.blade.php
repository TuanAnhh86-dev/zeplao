<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-800 text-white antialiased">
    @include('partials.site-header')

    @php($isAuraEvent = $event->slug === 'the-aura-khong-the-thay-the-nov-2026')
    <main class="mx-auto max-w-[920px] px-4 py-4 sm:px-5 sm:py-5">
        <a href="{{ route('dashboard') }}" class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-violet-300 transition hover:border-violet-400 hover:bg-violet-500/10"><span aria-hidden="true">&larr;</span> Tr&#7903; v&#7873;</a>
        <section class="relative grid overflow-hidden rounded-[20px] bg-neutral-700 lg:min-h-[260px] lg:grid-cols-[32%_68%]" aria-labelledby="event-title">
            <span aria-hidden="true" class="pointer-events-none absolute left-[32%] top-0 z-30 hidden size-14 -translate-x-1/2 -translate-y-1/2 rounded-full bg-neutral-800 lg:block"></span>
            <span aria-hidden="true" class="pointer-events-none absolute bottom-0 left-[32%] z-30 hidden size-14 -translate-x-1/2 translate-y-1/2 rounded-full bg-neutral-800 lg:block"></span>
            <div class="relative z-10 flex flex-col justify-between border-b-2 border-dashed border-white/25 p-4 lg:border-b-0 lg:border-r-2 lg:p-5">
                <div>
                    <h1 id="event-title" class="text-lg font-bold leading-tight sm:text-xl">{{ $event->title }}</h1>
                    <div class="mt-5 space-y-3 text-sm font-semibold text-violet-300">
                        <p class="flex items-start gap-3"><span class="text-white" aria-hidden="true">&#9632;</span><span>{{ $event->starts_at->format('H:i, d/m/Y') }}</span></p>
                        <p class="flex items-start gap-3"><span class="text-white" aria-hidden="true">&#9679;</span><span>{{ $event->venue }}<span class="block pt-1 text-sm font-medium text-neutral-300">{{ $event->city }}</span></span></p>
                    </div>
                </div>
                <div class="mt-6 border-t border-white/50 pt-3">
                    <p class="flex flex-wrap items-center gap-2 text-base font-semibold"><span>Gi&#225; v&#233; t&#7915;</span><span class="text-xl font-bold text-violet-300">{{ number_format($event->ticketTypes->min('price') ?? 0, 0, ',', '.') }} &#273;</span></p>
                    <a href="{{ route('ticket-detail', ['event' => $event->slug]) }}" class="mt-3 flex h-10 w-full items-center justify-center rounded-md bg-violet-600 px-4 text-sm font-bold text-white transition hover:bg-violet-500">Mua v&#233; ngay</a>
                </div>
            </div>
            <div class="relative min-h-48 overflow-hidden {{ $isAuraEvent ? 'bg-neutral-700' : 'bg-neutral-950' }} lg:min-h-[260px]"><img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" class="absolute inset-0 size-full object-cover {{ $isAuraEvent ? 'object-right' : 'object-center' }}"></div>
        </section>

        <section class="mt-8 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]" aria-label="Gi&#7899;i thi&#7879;u v&#224; th&#244;ng tin v&#233;">
            <article class="rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-7" aria-labelledby="event-introduction-heading">
                <p class="text-sm font-semibold uppercase tracking-wide text-violet-300">{{ $event->starts_at->format('d/m/Y') }} &middot; {{ $event->city }}</p>
                <h2 id="event-introduction-heading" class="mt-2 text-2xl font-bold">V&#7873; s&#7921; ki&#7879;n</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-neutral-300 sm:text-base">{{ $event->introduction ?: 'Th&#244;ng tin gi&#7899;i thi&#7879;u s&#7921; ki&#7879;n &#273;ang &#273;&#432;&#7907;c c&#7853;p nh&#7853;t.' }}</p>
                @if ($introductionImages->isNotEmpty())
                    <div class="mt-5 space-y-4">@foreach ($introductionImages as $image)<img src="{{ asset($image) }}" alt="{{ $event->title }}" loading="lazy" class="h-auto w-full rounded-xl object-cover">@endforeach</div>
                @endif
            </article>
            <section class="rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-7" aria-labelledby="ticket-options-heading">
                <p class="text-sm font-semibold uppercase tracking-wide text-violet-300">{{ $event->ticketTypes->count() }} h&#7841;ng v&#233;</p>
                <h2 id="ticket-options-heading" class="mt-2 text-2xl font-bold">Th&#244;ng tin v&#233;</h2>
                <p class="mt-5 text-sm leading-6 text-neutral-300">Gi&#225; v&#233; hi&#7875;n th&#7883; t&#7915; m&#7913;c th&#7845;p nh&#7845;t. Xem c&#225;c h&#7841;ng v&#233; v&#224; ch&#7885;n s&#7889; l&#432;&#7907;ng &#7903; b&#432;&#7899;c ti&#7871;p theo.</p>
                <a href="{{ route('ticket-detail', ['event' => $event->slug]) }}" class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-full bg-violet-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-violet-500">Ch&#7885;n h&#7841;ng v&#233; <span class="ml-2" aria-hidden="true">&rarr;</span></a>
            </section>
        </section>
    </main>
</body>
</html>
