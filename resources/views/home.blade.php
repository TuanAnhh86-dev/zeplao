<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-50 text-neutral-950 antialiased">
    <header class="border-b border-neutral-200 bg-white">
        <div class="mx-auto flex min-h-16 max-w-6xl items-center justify-between px-5 sm:px-8">
            <a href="{{ route('dashboard') }}" class="text-2xl font-black tracking-[-0.08em]" aria-label="Tixtak">
                tix<span class="text-violet-600">tak</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-full border border-neutral-300 px-5 py-2 text-sm font-semibold transition hover:border-neutral-950 hover:bg-neutral-950 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-950">
                    Đăng xuất
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-12 sm:px-8 sm:py-16">
        <p class="text-sm text-neutral-500">Tixtak</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Dashboard</h1>
        <p class="mt-3 text-neutral-600">Xin chào, {{ auth()->user()->name }}.</p>
    </main>
</body>
</html>
