<header class="sticky top-0 z-40 border-b border-neutral-800 bg-neutral-950/95 text-white shadow-sm backdrop-blur">
    <div class="mx-auto flex min-h-[76px] max-w-[1800px] flex-wrap items-center gap-4 px-5 py-3 xl:flex-nowrap xl:px-8">
        <a href="{{ route('dashboard') }}" class="relative block h-12 w-[142px] shrink-0 overflow-hidden" aria-label="Tixtak">
            <img src="{{ asset('images/logo_web.png') }}" alt="Tixtak" class="absolute left-1/2 top-1/2 h-[160px] w-[160px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain">
        </a>

        <form role="search" action="{{ route('dashboard') }}" method="GET" class="order-3 flex h-12 w-full items-center overflow-hidden rounded-xl border border-white/10 bg-neutral-900 xl:order-none xl:ml-6 xl:max-w-[540px]" data-search-form>
            <label for="event-search" class="sr-only">T&#236;m s&#7921; ki&#7879;n</label>
            <svg class="ml-4 size-5 shrink-0 text-neutral-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="2.2" />
                <path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
            </svg>
            <input id="event-search" name="q" type="search" placeholder="B&#7841;n t&#236;m s&#7921; ki&#7879;n n&#224;o h&#244;m nay?" class="h-full min-w-0 flex-1 border-0 bg-transparent px-4 text-sm text-white outline-none placeholder:text-neutral-400 focus:ring-0" data-event-search>
            <button type="submit" class="h-full shrink-0 border-l border-white/10 px-5 text-sm font-semibold transition hover:bg-violet-600">T&#236;m ki&#7871;m</button>
        </form>

        <nav class="ml-auto flex items-center gap-1 sm:gap-3" aria-label="&#272;i&#7873;u h&#432;&#7899;ng ch&#237;nh">
            <a href="{{ route('dashboard') }}#my-tickets" class="inline-flex h-11 items-center gap-2 rounded-full px-3 text-sm font-semibold text-white transition hover:bg-white/10 hover:text-violet-300 sm:px-4">
                <svg class="size-5 text-violet-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5A2.5 2.5 0 0 0 6.5 5h11A2.5 2.5 0 0 0 20 7.5v2a2.5 2.5 0 0 0 0 5v2a2.5 2.5 0 0 0-2.5 2.5h-11A2.5 2.5 0 0 0 4 16.5v-2a2.5 2.5 0 0 0 0-5v-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8v2m0 2v2m0 2v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span class="hidden sm:inline">V&#233; c&#7911;a t&#244;i</span>
            </a>

            <details class="group relative">
                <summary class="flex h-11 cursor-pointer list-none items-center gap-2 rounded-full px-2 transition hover:bg-white/10 [&::-webkit-details-marker]:hidden">
                    <span class="grid size-9 place-items-center rounded-full bg-violet-600 text-sm font-bold text-white" aria-hidden="true">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.8"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <span class="hidden max-w-32 truncate text-sm font-medium md:inline">{{ auth()->user()->name }}</span>
                    <svg class="size-4 text-neutral-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.2 7.4a.75.75 0 0 1 1.06 0L10 11.14l3.74-3.74a.75.75 0 1 1 1.06 1.06l-4.27 4.27a.75.75 0 0 1-1.06 0L5.2 8.46a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </summary>
                <div class="absolute right-0 top-12 z-50 hidden w-64 rounded-xl border border-neutral-200 bg-white p-2 text-sm text-neutral-900 shadow-xl group-open:block">
                    <div class="border-b border-neutral-100 px-3 py-2">
                        <p class="truncate font-semibold">{{ auth()->user()->name }}</p>
                        <p class="mt-1 truncate text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('dashboard') }}#my-tickets" class="mt-1 block rounded-lg px-3 py-2 text-neutral-700 transition hover:bg-violet-50 hover:text-violet-700">V&#233; c&#7911;a t&#244;i</a>
                    <div class="px-3 py-2 text-xs text-neutral-500">H&#7891; s&#417; c&#225; nh&#226;n</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left font-medium text-neutral-700 transition hover:bg-neutral-100">&#272;&#259;ng xu&#7845;t</button>
                    </form>
                </div>
            </details>
        </nav>
    </div>
</header>
