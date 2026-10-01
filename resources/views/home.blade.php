<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sự kiện sắp diễn ra | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-white antialiased">
    @include('partials.site-header')

    <main class="mx-auto max-w-[1800px] px-5 pb-16 pt-8 sm:px-8 sm:pt-10">
        <div class="flex flex-col gap-5 border-b border-white/10 pb-7 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium text-violet-300">Khám phá sự kiện</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Sắp diễn ra</h1>
                <p class="mt-2 text-sm text-neutral-400">Chọn sự kiện và tìm loại vé phù hợp với bạn.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-white/10 bg-neutral-900/70 p-3 shadow-xl shadow-black/20">
                <label class="sr-only" for="date-filter">Thời gian</label>
                <select id="date-filter" class="h-11 cursor-pointer rounded-xl border border-white/10 bg-neutral-800 px-4 text-sm font-semibold text-white outline-none transition hover:border-violet-400/70 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20" data-date-filter>
                    <option class="bg-neutral-900 text-white" value="all">Tất cả ngày</option>
                    <option class="bg-neutral-900 text-white" value="30">30 ngày tới</option>
                    <option class="bg-neutral-900 text-white" value="90">90 ngày tới</option>
                </select>
                <label class="sr-only" for="category-filter">Thể loại</label>
                <select id="category-filter" class="h-11 cursor-pointer rounded-xl border border-violet-400/40 bg-violet-500/10 px-4 text-sm font-semibold text-white outline-none transition hover:border-violet-300 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20" data-category-filter>
                    <option class="bg-neutral-900 text-white" value="all">Tất cả thể loại</option>
                    <option class="bg-neutral-900 text-white" value="music">Nhạc sống</option>
                    <option class="bg-neutral-900 text-white" value="festival">Lễ hội</option>
                    <option class="bg-neutral-900 text-white" value="theatre">Sân khấu</option>
                    <option class="bg-neutral-900 text-white" value="experience">Trải nghiệm</option>
                </select>
                <label class="sr-only" for="city-filter">Thành phố</label>
                <select id="city-filter" class="h-11 cursor-pointer rounded-xl border border-white/10 bg-neutral-800 px-4 text-sm font-semibold text-white outline-none transition hover:border-violet-400/70 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20" data-city-filter>
                    <option class="bg-neutral-900 text-white" value="all">Mọi địa điểm</option>
                    <option class="bg-neutral-900 text-white" value="hcm">TP. Hồ Chí Minh</option>
                    <option class="bg-neutral-900 text-white" value="hanoi">Hà Nội</option>
                    <option class="bg-neutral-900 text-white" value="danang">Đà Nẵng</option>
                    <option class="bg-neutral-900 text-white" value="dalat">Đà Lạt</option>
                </select>
            </div>
        </div>

        @if ($featuredEvents->isNotEmpty())
            <section class="-mx-5 mt-8 bg-neutral-900 px-5 py-7 sm:-mx-8 sm:px-8" aria-labelledby="year-end-heading">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 id="year-end-heading" class="text-xl font-semibold sm:text-2xl">Sự kiện sắp diễn ra</h2>
                        <div class="mt-2 h-1 w-28 rounded-full bg-violet-500"></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden text-sm text-neutral-400 sm:inline">{{ $featuredEvents->count() }} sự kiện</span>
                        <button type="button" data-carousel-prev aria-label="Cuộn sự kiện sang trái" class="grid size-10 place-items-center rounded-full border border-white/15 transition hover:border-violet-400 hover:bg-violet-500/15">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14.5 5-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" data-carousel-next aria-label="Cuộn sự kiện sang phải" class="grid size-10 place-items-center rounded-full border border-white/15 transition hover:border-violet-400 hover:bg-violet-500/15">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9.5 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
                <div data-event-carousel class="-mx-1 flex snap-x snap-mandatory gap-5 overflow-x-auto px-1 pb-3 [scrollbar-width:thin] [scrollbar-color:#7c3aed_#262626]">
                    @foreach ($featuredEvents as $event)
                        <article class="group w-[min(82vw,360px)] shrink-0 snap-start overflow-hidden rounded-2xl bg-neutral-950 transition hover:-translate-y-1" data-event-card data-carousel-card data-category="{{ $event->category }}" data-city="{{ $event->city_key }}" data-date="{{ $event->starts_at->format('Y-m-d') }}" data-search="{{ \Illuminate\Support\Str::lower($event->title.' '.$event->category.' '.$event->city.' '.$event->venue) }}">
                            <div class="overflow-hidden">
                                <img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" loading="lazy" class="aspect-[16/9] w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                            <div class="p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-violet-300">Tháng {{ $event->starts_at->format('m/Y') }}</p>
                                <h3 class="mt-2 line-clamp-2 min-h-12 text-base font-bold leading-6">{{ $event->title }}</h3>
                                <p class="mt-3 font-bold text-emerald-400">Từ {{ number_format($event->ticketTypes->min('price') ?? 0, 0, ',', '.') }}đ</p>
                                <div class="mt-2 flex items-center justify-between gap-3 text-sm text-neutral-400">
                                    <span>{{ $event->starts_at->format('d/m/Y') }}</span>
                                    <a href="{{ route('select-ticket', ['event' => $event->slug]) }}" class="shrink-0 font-semibold text-white underline decoration-violet-400 underline-offset-4 hover:text-violet-300">Xem vé</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section id="events" class="pt-7" aria-labelledby="events-heading">
            <div class="mb-5 flex items-center justify-between">
                <h2 id="events-heading" class="text-xl font-semibold sm:text-2xl">Sự kiện sắp tới</h2>
                <span class="text-xs text-neutral-500 sm:text-sm" data-event-count>{{ $events->count() }} sự kiện</span>
            </div>

            @if ($events->isEmpty())
                <div class="rounded-2xl border border-dashed border-white/15 px-5 py-14 text-center text-neutral-400">
                    Hiện chưa có sự kiện sắp diễn ra.
                </div>
            @else
                <div class="grid grid-cols-1 gap-x-5 gap-y-8 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4" data-event-grid>
                    @foreach ($events as $event)
                        @php
                            $categoryLabels = ['music' => 'Nhạc sống', 'festival' => 'Lễ hội', 'theatre' => 'Sân khấu', 'experience' => 'Trải nghiệm'];
                        @endphp
                        <article
                            class="group min-w-0 overflow-hidden rounded-2xl border border-white/10 bg-neutral-900 transition duration-200 hover:-translate-y-1 hover:border-violet-400/50 hover:shadow-xl hover:shadow-violet-950/20"
                            data-event-card
                            data-category="{{ $event->category }}"
                            data-city="{{ $event->city_key }}"
                            data-date="{{ $event->starts_at->format('Y-m-d') }}"
                            data-search="{{ \Illuminate\Support\Str::lower($event->title.' '.$event->category.' '.$event->city.' '.$event->venue) }}"
                        >
                            <div class="relative aspect-[16/10] overflow-hidden bg-neutral-800">
                                <img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
                                <span class="absolute left-3 top-3 rounded-full bg-neutral-950/80 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $categoryLabels[$event->category] ?? $event->category }}</span>
                                <span class="absolute bottom-3 left-3 rounded-lg border border-white/15 bg-neutral-950/80 px-3 py-1.5 text-xs font-medium text-white backdrop-blur">{{ $event->ticketTypes->first()?->name ?? 'Vé sự kiện' }}</span>
                            </div>
                            <div class="p-4 sm:p-5">
                                <h3 class="line-clamp-2 min-h-12 text-base font-semibold leading-6 text-white sm:text-lg">{{ $event->title }}</h3>
                                <div class="mt-3 flex items-end justify-between gap-3 rounded-xl border border-violet-400/15 bg-violet-500/[0.06] px-3.5 py-3">
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Giá vé từ</p>
                                        <p class="mt-0.5 text-base font-bold text-violet-300">{{ number_format($event->ticketTypes->min('price') ?? 0, 0, ',', '.') }}đ</p>
                                    </div>
                                    <a href="{{ route('select-ticket', ['event' => $event->slug]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-violet-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-violet-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-400" aria-label="Xem vé {{ $event->title }}">
                                        Xem vé <svg class="size-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7 4h9v9M16 4 8 12M14 11v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                                <div class="mt-4 space-y-2 text-sm text-neutral-400">
                                    <p class="flex items-center gap-2">
                                        <svg class="size-4 shrink-0 text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5" width="17" height="16" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M7.5 3v4M16.5 3v4M3.5 9h17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                        {{ $event->starts_at->format('d/m/Y · H:i') }}
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <svg class="size-4 shrink-0 text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg>
                                        <span class="truncate">{{ $event->venue }} · {{ $event->city }}</span>
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="hidden rounded-2xl border border-dashed border-white/15 px-5 py-12 text-center text-sm text-neutral-400" data-no-results>Không tìm thấy sự kiện phù hợp. Hãy thử đổi từ khóa hoặc bộ lọc.</p>
            @endif
        </section>

        <section id="my-tickets" class="mt-14 border-t border-white/10 pt-8" aria-labelledby="my-tickets-heading">
            <h2 id="my-tickets-heading" class="text-xl font-semibold sm:text-2xl">Vé của tôi</h2>
            <div class="mt-4 rounded-2xl border border-white/10 bg-neutral-900 px-5 py-8 text-sm text-neutral-400">
                Chưa có vé nào được đặt trên tài khoản này.
            </div>
        </section>
    </main>

    <script>
        (() => {
            const searchForm = document.querySelector('[data-search-form]');
            const searchInput = document.querySelector('[data-event-search]');
            const dateFilter = document.querySelector('[data-date-filter]');
            const categoryFilter = document.querySelector('[data-category-filter]');
            const cityFilter = document.querySelector('[data-city-filter]');
            const cards = [...document.querySelectorAll('[data-event-card]')];
            const noResults = document.querySelector('[data-no-results]');
            const eventCount = document.querySelector('[data-event-count]');
            const carousel = document.querySelector('[data-event-carousel]');

            document.querySelector('[data-carousel-prev]')?.addEventListener('click', () => carousel?.scrollBy({ left: -380, behavior: 'smooth' }));
            document.querySelector('[data-carousel-next]')?.addEventListener('click', () => carousel?.scrollBy({ left: 380, behavior: 'smooth' }));

            searchForm.addEventListener('submit', event => event.preventDefault());

            const filterEvents = () => {
                const query = searchInput.value.trim().toLocaleLowerCase('vi');
                const days = dateFilter.value === 'all' ? Infinity : Number(dateFilter.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                let visibleCount = 0;

                cards.forEach(card => {
                    const daysUntilEvent = (new Date(`${card.dataset.date}T00:00:00`) - today) / 86400000;
                    const visible = card.dataset.search.includes(query)
                        && (categoryFilter.value === 'all' || card.dataset.category === categoryFilter.value)
                        && (cityFilter.value === 'all' || card.dataset.city === cityFilter.value)
                        && daysUntilEvent >= 0
                        && daysUntilEvent <= days;

                    card.classList.toggle('hidden', !visible);
                    if (visible && !card.hasAttribute('data-carousel-card')) visibleCount++;
                });

                noResults?.classList.toggle('hidden', visibleCount > 0);
                eventCount.textContent = `${visibleCount} sự kiện`;
            };

            searchInput.addEventListener('input', filterEvents);
            dateFilter.addEventListener('change', filterEvents);
            categoryFilter.addEventListener('change', filterEvents);
            cityFilter.addEventListener('change', filterEvents);
            filterEvents();
        })();
    </script>
</body>
</html>
