<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-white antialiased">
    <header class="sticky top-0 z-30 border-b border-white/10 bg-neutral-950/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-3 sm:px-6">
            <a href="{{ route('admin.dashboard') }}" class="mr-auto flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-violet-600 font-black">T</span>
                <span><span class="block font-bold">Tixtak</span><span class="block text-xs text-neutral-400">Khu vực quản trị</span></span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-3 py-2 text-sm text-neutral-300 hover:bg-white/10 hover:text-white">Tổng quan</a>
            <a href="{{ route('admin.events') }}" class="rounded-xl px-3 py-2 text-sm text-neutral-300 hover:bg-white/10 hover:text-white">Sự kiện</a>
            <a href="{{ route('admin.orders') }}" class="rounded-xl px-3 py-2 text-sm text-neutral-300 hover:bg-white/10 hover:text-white">Giao dịch</a>
            <a href="{{ route('admin.customers') }}" class="rounded-xl px-3 py-2 text-sm text-neutral-300 hover:bg-white/10 hover:text-white">Khách hàng</a>
            <span class="hidden text-sm text-neutral-400 sm:inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-full border border-white/15 px-4 py-2 text-sm hover:border-violet-400 hover:text-violet-300">Đăng xuất</button></form>
        </div>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10">
        @if (session('status'))<div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="mb-6 rounded-xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</body>
</html>
