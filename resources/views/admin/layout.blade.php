<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#09090b] text-white antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,rgba(124,58,237,.12),transparent_48%)]"></div>
    <header class="sticky top-0 z-30 border-b border-white/10 bg-neutral-950/90 shadow-2xl shadow-black/20 backdrop-blur-xl">
        <div class="mx-auto flex max-w-[1600px] flex-wrap items-center gap-4 px-4 py-3 sm:px-6 xl:flex-nowrap xl:px-8">
            <a href="{{ route('admin.dashboard') }}" class="mr-auto flex shrink-0 items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-violet-600 text-lg font-black shadow-lg shadow-violet-950/40">T</span>
                <span><span class="block font-bold tracking-wide">Tixtak</span><span class="block text-[11px] text-neutral-500">Khu vực quản trị</span></span>
            </a>
            <nav class="order-3 flex w-full gap-1 overflow-x-auto border-t border-white/5 pt-2 xl:order-none xl:w-auto xl:border-0 xl:pt-0" aria-label="Điều hướng quản trị">
                <a href="{{ route('admin.dashboard') }}" @class(['whitespace-nowrap rounded-xl px-3 py-2 text-sm transition', 'bg-violet-500/15 font-semibold text-violet-200 ring-1 ring-violet-400/20' => request()->routeIs('admin.dashboard'), 'text-neutral-400 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.dashboard')])>Tổng quan</a>
                <a href="{{ route('admin.events') }}" @class(['whitespace-nowrap rounded-xl px-3 py-2 text-sm transition', 'bg-violet-500/15 font-semibold text-violet-200 ring-1 ring-violet-400/20' => request()->routeIs('admin.events*'), 'text-neutral-400 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.events*')])>Sự kiện</a>
                <a href="{{ route('admin.orders') }}" @class(['whitespace-nowrap rounded-xl px-3 py-2 text-sm transition', 'bg-violet-500/15 font-semibold text-violet-200 ring-1 ring-violet-400/20' => request()->routeIs('admin.orders*'), 'text-neutral-400 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.orders*')])>Đơn vé</a>
                <a href="{{ route('admin.customers') }}" @class(['whitespace-nowrap rounded-xl px-3 py-2 text-sm transition', 'bg-violet-500/15 font-semibold text-violet-200 ring-1 ring-violet-400/20' => request()->routeIs('admin.customers'), 'text-neutral-400 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.customers')])>Khách hàng</a>
            </nav>
            <div class="flex shrink-0 items-center gap-3 text-sm">
                <span class="hidden max-w-40 truncate text-neutral-400 md:block">{{ auth()->user()->name }}</span>
                <a href="{{ route('admin.qr-scan') }}" class="rounded-full bg-violet-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-violet-500">Quét QR</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold text-neutral-300 transition hover:border-rose-400/30 hover:bg-rose-400/10 hover:text-rose-200">Đăng xuất</button></form>
            </div>
        </div>
    </header>
    <main class="mx-auto max-w-[1600px] px-4 py-7 sm:px-6 sm:py-9 xl:px-8">
        @if (session('status'))<div class="mb-6 rounded-2xl border border-emerald-300/15 bg-emerald-300/[.07] px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-black/10">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="mb-6 rounded-2xl border border-rose-300/15 bg-rose-300/[.07] px-4 py-3 text-sm text-rose-200 shadow-lg shadow-black/10">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</body>
</html>
