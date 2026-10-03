<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập | Tixtak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-neutral-950 antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[532px] px-4 pb-[34px] pt-2 sm:px-7 sm:pt-3">
        <a href="{{ route('login') }}" class="relative block h-[53px] w-[162px] overflow-hidden" aria-label="Tixtak">
            <img src="{{ asset('images/logo.png') }}" alt="Tixtak" class="absolute left-1/2 top-1/2 h-[182px] w-[182px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain">
        </a>

        <section class="mt-4 sm:mt-5">
            <h1 class="max-w-[434px] text-[21px] font-medium leading-tight tracking-[-0.03em] sm:text-[24px]">
                Đăng nhập hoặc tham gia Tixtak
            </h1>
            <p class="mt-[14px] text-[13px] leading-5 text-neutral-700">Việt Nam</p>

            @if (session('status'))
                <div class="mt-[17px] rounded-xl bg-green-50 px-[11px] py-[8px] text-[10px] text-green-800" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.authenticate') }}" class="mt-[25px]" data-login-form data-clear-password="{{ $errors->has('email') ? 'true' : 'false' }}">
                @csrf

                <div>
                    <label for="email" class="sr-only">Địa chỉ Gmail</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        inputmode="email"
                        required
                        autofocus
                        placeholder="Gmail*"
                        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                        class="block h-[50px] w-full rounded-lg border border-neutral-400 bg-white px-[14px] text-[13px] outline-none transition placeholder:text-neutral-500 focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950 aria-[invalid=true]:border-red-600"
                    >
                    @error('email')
                        <p class="mt-1 text-[10px] text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-[14px]">
                    <label for="password" class="sr-only">Mật khẩu</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="{{ $errors->has('email') ? 'new-password' : 'current-password' }}"
                        required
                        placeholder="Mật khẩu*"
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        class="block h-[50px] w-full rounded-lg border border-neutral-400 bg-white px-[14px] text-[13px] outline-none transition placeholder:text-neutral-500 focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950 aria-[invalid=true]:border-red-600"
                    >
                    @error('password')
                        <p class="mt-1 text-[10px] text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-[14px] flex justify-end">
                    <a href="{{ route('password.request') }}" class="text-[11px] font-medium text-neutral-600 underline underline-offset-2 hover:text-neutral-950">
                        Quên mật khẩu?
                    </a>
                </div>

                <p class="mt-[22px] max-w-[350px] text-[11px] leading-5 text-neutral-600">
                    Bằng cách tiếp tục, bạn đồng ý với
                    <a href="#privacy" class="font-medium underline underline-offset-2">Chính sách quyền riêng tư</a>
                    và <a href="#terms" class="font-medium underline underline-offset-2">Điều khoản sử dụng</a> của Tixtak.
                </p>

                <div class="mt-[22px] flex justify-end">
                    <button type="submit" class="flex h-[41px] min-w-[105px] items-center justify-center rounded-full bg-neutral-950 px-[22px] text-[11px] font-semibold text-white transition hover:bg-neutral-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-950">
                        Đăng nhập
                    </button>
                </div>
            </form>

            <p class="mt-[22px] text-[11px] text-neutral-700">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="font-semibold text-neutral-950 underline underline-offset-2">Đăng ký</a>
            </p>
        </section>
    </main>
    <script>
        (() => {
            const form = document.querySelector('[data-login-form]');
            if (form?.dataset.clearPassword !== 'true') return;

            const clearPassword = () => {
                const password = form.querySelector('input[name="password"]');
                if (password) password.value = '';
            };

            clearPassword();
            window.addEventListener('pageshow', clearPassword);
            window.setTimeout(clearPassword, 100);
        })();
    </script>
</body>
</html>
