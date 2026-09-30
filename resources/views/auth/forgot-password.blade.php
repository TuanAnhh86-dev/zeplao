<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quên mật khẩu | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-neutral-950 antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[532px] px-4 pb-8 pt-2 sm:px-7 sm:pt-3">
        <a href="{{ route('login') }}" class="relative block h-[53px] w-[162px] overflow-hidden" aria-label="Tixtak">
            <img src="{{ asset('images/logo.png') }}" alt="Tixtak" class="absolute left-1/2 top-1/2 h-[182px] w-[182px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain">
        </a>

        <section class="mt-4 sm:mt-5">
            @if ($email)
                <h1 class="text-[21px] font-medium leading-tight tracking-[-0.03em] sm:text-[24px]">Nhập mã 8 chữ số gửi tới Gmail</h1>
                <p class="mt-3 break-all text-[13px] text-neutral-700">
                    {{ $email }}
                    <a href="{{ route('password.request', ['edit' => 1]) }}" class="ml-1 text-neutral-500 underline underline-offset-2">Sửa</a>
                </p>
            @else
                <h1 class="text-[21px] font-medium leading-tight tracking-[-0.03em] sm:text-[24px]">Quên mật khẩu?</h1>
                <p class="mt-3 text-[13px] leading-5 text-neutral-700">Nhập Gmail để nhận mã xác minh và tạo mật khẩu mới.</p>
            @endif

            @if (session('status'))
                <p class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-[11px] text-green-800" role="status">{{ session('status') }}</p>
            @endif

            @if ($email)
                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-3">
                    @csrf
                    <div>
                        <label for="code" class="sr-only">Mã OTP 8 chữ số</label>
                        <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{8}" maxlength="8" required autofocus placeholder="Mã 8 chữ số*" value="{{ old('code') }}" class="block h-[50px] w-full rounded-lg border border-neutral-400 bg-white px-[14px] text-[13px] outline-none placeholder:text-neutral-500 focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950">
                        @error('code')<p class="mt-1 text-[10px] text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="sr-only">Mật khẩu mới</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" minlength="6" required placeholder="Mật khẩu mới (ít nhất 6 ký tự)*" class="block h-[50px] w-full rounded-lg border border-neutral-400 bg-white px-[14px] text-[13px] outline-none placeholder:text-neutral-500 focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950">
                        @error('password')<p class="mt-1 text-[10px] text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">Xác nhận mật khẩu mới</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="6" required placeholder="Xác nhận mật khẩu mới*" class="block h-[50px] w-full rounded-lg border border-neutral-400 bg-white px-[14px] text-[13px] outline-none placeholder:text-neutral-500 focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950">
                    </div>
                    <button type="submit" class="mt-3 flex h-[41px] w-full items-center justify-center rounded-full bg-neutral-950 px-5 text-[11px] font-semibold text-white hover:bg-neutral-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-950">Cập nhật mật khẩu</button>
                </form>
                <form method="POST" action="{{ route('password.email') }}" class="mt-3 flex justify-end">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="text-[11px] text-neutral-600 underline underline-offset-2 hover:text-neutral-950">Gửi lại mã</button>
                </form>
            @else
                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="sr-only">Gmail</label>
                        <input id="email" name="email" type="email" inputmode="email" autocomplete="email" required autofocus placeholder="Gmail*" value="{{ old('email') }}" class="block h-[50px] w-full rounded-lg border border-neutral-400 bg-white px-[14px] text-[13px] outline-none placeholder:text-neutral-500 focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950">
                        @error('email')<p class="mt-1 text-[10px] text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="flex h-[41px] w-full items-center justify-center rounded-full bg-neutral-950 px-5 text-[11px] font-semibold text-white hover:bg-neutral-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-950">Gửi mã OTP</button>
                </form>
            @endif

            <a href="{{ route('login') }}" class="mt-6 inline-block text-[11px] text-neutral-600 underline underline-offset-2">Quay lại đăng nhập</a>
        </section>
    </main>
</body>
</html>
