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

    <main class="mx-auto max-w-[1050px] px-4 py-6 sm:px-6 sm:py-8">
        <section class="grid overflow-hidden rounded-[24px] bg-neutral-700 lg:min-h-[350px] lg:grid-cols-[minmax(300px,0.72fr)_minmax(0,1.28fr)]" aria-labelledby="event-title">
            <div class="relative z-10 flex flex-col justify-between border-b-2 border-dashed border-white/25 p-6 sm:p-8 before:absolute before:-right-8 before:-top-8 before:z-20 before:hidden before:size-16 before:rounded-full before:bg-neutral-800 after:absolute after:-right-8 after:-bottom-8 after:z-20 after:hidden after:size-16 after:rounded-full after:bg-neutral-800 lg:border-b-0 lg:border-r-2 lg:p-8 lg:before:block lg:after:block xl:p-9">
                <div>
                    <h1 id="event-title" class="text-xl font-bold leading-tight sm:text-2xl">{{ $event->title }}</h1>

                    <div class="mt-7 space-y-4 text-sm font-semibold text-violet-300 sm:text-base">
                        <p class="flex items-start gap-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a1 1 0 0 1 1-1Zm12 8H5v9h14v-9Z"/></svg>
                            <span>{{ $event->starts_at->format('H:i, d/m/Y') }}</span>
                        </p>
                        <p class="flex items-start gap-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a8 8 0 0 0-8 8c0 5.6 8 12 8 12s8-6.4 8-12a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg>
                            <span>{{ $event->venue }}<span class="block pt-1 text-sm font-medium text-neutral-300">{{ $event->city }}</span></span>
                        </p>
                    </div>
                </div>

                <div class="mt-10 border-t border-white/50 pt-4">
                    <p class="flex items-center gap-3 text-lg font-semibold">
                        <span>Gi&#225; t&#7915;</span>
                        <span class="text-2xl font-bold text-violet-300">{{ number_format($event->ticketTypes->min('price') ?? 0, 0, ',', '.') }} &#273;</span>
                        <span class="text-2xl text-violet-300" aria-hidden="true">&rsaquo;</span>
                    </p>
                    <a href="#ticket-options" class="mt-4 flex h-11 w-full items-center justify-center rounded-md bg-violet-600 px-5 text-sm font-bold text-white transition hover:bg-violet-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-300">Mua v&#233; ngay</a>
                </div>
            </div>

            <div class="min-h-56 overflow-hidden bg-neutral-900 lg:min-h-full">
                <img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" class="h-full min-h-56 w-full object-cover lg:min-h-[350px]">
            </div>
        </section>

        <section class="mt-8 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]" aria-label="Gi&#7899;i thi&#7879;u v&#224; th&#244;ng tin v&#233;">
            <article class="rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-7" aria-labelledby="event-introduction-heading">
                <p class="text-sm font-semibold uppercase tracking-wide text-violet-300">{{ $event->starts_at->format('d/m/Y') }} &middot; {{ $event->city }}</p>
                <h2 id="event-introduction-heading" class="mt-2 text-2xl font-bold">V&#7873; s&#7921; ki&#7879;n</h2>
                @php
                    $introduction = preg_replace('/\s+/', ' ', trim($event->introduction ?? ''));
                @endphp
                <details class="group mt-4">
                    <summary class="cursor-pointer list-none [&::-webkit-details-marker]:hidden focus-visible:outline focus-visible:outline-2 focus-visible:outline-violet-400">
                        <p class="line-clamp-4 text-sm leading-7 text-neutral-300 group-open:line-clamp-none sm:text-base">{{ $introduction ?: html_entity_decode('Th&#244;ng tin gi&#7899;i thi&#7879;u s&#7921; ki&#7879;n &#273;ang &#273;&#432;&#7907;c c&#7853;p nh&#7853;t.', ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-violet-300">
                            <span class="group-open:hidden">Xem chi ti&#7871;t s&#7921; ki&#7879;n</span>
                            <span class="hidden group-open:inline">Thu g&#7885;n n&#7897;i dung</span>
                            <svg class="size-4 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </summary>
                </details>
            </article>

            <section id="ticket-options" class="scroll-mt-24 rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-7" aria-labelledby="ticket-options-heading">
                <div class="mb-5">
                    <p class="text-sm font-semibold uppercase tracking-wide text-violet-300">{{ $event->ticketTypes->count() }} h&#7841;ng v&#233;</p>
                    <h2 id="ticket-options-heading" class="mt-2 text-2xl font-bold">Th&#244;ng tin v&#233;</h2>
                </div>

                <div class="space-y-3">
                    @forelse ($event->ticketTypes as $ticketType)
                        <article class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-white/10 bg-neutral-800 p-4">
                            <div>
                                <h3 class="font-semibold">{{ $ticketType->name }}</h3>
                                <p class="mt-1 text-lg font-bold text-violet-300">{{ number_format($ticketType->price, 0, ',', '.') }} &#273;</p>
                            </div>
                            <span class="rounded-full bg-violet-500/10 px-3 py-1.5 text-xs font-semibold text-violet-300">H&#7841;ng v&#233;</span>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-white/15 p-5 text-sm text-neutral-400">Hi&#7879;n ch&#432;a c&#243; th&#244;ng tin lo&#7841;i v&#233; cho s&#7921; ki&#7879;n n&#224;y.</div>
                    @endforelse
                </div>
            </section>
        </section>
    </main>
</body>
</html>
