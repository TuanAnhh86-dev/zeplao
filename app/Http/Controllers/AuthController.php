<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function forgotPassword(Request $request): View
    {
        if ($request->boolean('edit')) {
            $email = $request->session()->pull('password_reset_email');
            if ($email) {
                Cache::forget($this->otpCacheKey('password-reset', $email));
            }
        }

        return view('auth.forgot-password', [
            'email' => session('password_reset_email'),
        ]);
    }

    public function sendPasswordResetCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);
        $email = mb_strtolower($validated['email']);
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Không tìm thấy tài khoản với Gmail này.'])
                ->withInput();
        }

        try {
            $this->sendOtp($email, 'password-reset');
        } catch (Throwable) {
            Cache::forget($this->otpCacheKey('password-reset', $email));

            return back()
                ->withErrors(['email' => 'Không gửi được mã. Hãy kiểm tra cấu hình Gmail SMTP trong .env.'])
                ->withInput();
        }

        $request->session()->put('password_reset_email', $email);

        return redirect()->route('password.request')->with('status', 'Mã xác minh 8 chữ số đã được gửi tới Gmail của bạn.');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $validated = $request->validate([
            'code' => ['required', 'digits:8'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $key = $this->otpCacheKey('password-reset', $email);
        $storedHash = Cache::get($key);

        if (! $storedHash || ! Hash::check($validated['code'], $storedHash)) {
            return back()->withErrors(['code' => 'Mã xác minh không đúng hoặc đã hết hạn.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            Cache::forget($key);
            $request->session()->forget('password_reset_email');

            return redirect()->route('password.request')->withErrors(['email' => 'Tài khoản không còn tồn tại.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);
        Cache::forget($key);
        $request->session()->forget('password_reset_email');

        return redirect()->route('login')->with('status', 'Mật khẩu đã được cập nhật. Hãy đăng nhập bằng mật khẩu mới.');
    }

    public function register(Request $request): View
    {
        if ($request->boolean('edit')) {
            $pending = $request->session()->pull('pending_registration');
            if ($pending) {
                Cache::forget($this->otpCacheKey('registration', $pending['email']));
            }
        }

        return view('auth.register', [
            'pendingRegistration' => session('pending_registration'),
        ]);
    }

    public function sendRegistrationCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);
        $email = mb_strtolower($validated['email']);

        try {
            $this->sendOtp($email, 'registration');
        } catch (Throwable) {
            Cache::forget($this->otpCacheKey('registration', $email));

            return back()
                ->withErrors(['email' => 'Không gửi được mã. Hãy kiểm tra cấu hình Gmail SMTP trong .env.'])
                ->withInput();
        }

        $pending = ['name' => $validated['name'], 'email' => $email];
        $request->session()->put('pending_registration', $pending);

        return redirect()->route('register')->with('status', 'Mã xác minh 8 chữ số đã được gửi tới Gmail của bạn.');
    }

    public function verifyRegistrationCode(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (! $pending) {
            return redirect()->route('register');
        }

        $validated = $request->validate([
            'code' => ['required', 'digits:8'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $key = $this->otpCacheKey('registration', $pending['email']);
        $storedHash = Cache::get($key);

        if (! $storedHash || ! Hash::check($validated['code'], $storedHash)) {
            return back()->withErrors(['code' => 'Mã xác minh không đúng hoặc đã hết hạn.']);
        }

        if (User::where('email', $pending['email'])->exists()) {
            Cache::forget($key);
            $request->session()->forget('pending_registration');

            return redirect()->route('register')->withErrors(['email' => 'Gmail này đã được đăng ký.']);
        }

        User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Cache::forget($key);
        $request->session()->forget(['pending_registration', 'url.intended']);
        $request->session()->regenerate();

        return redirect()->route('login')->with('status', 'Đăng ký thành công. Hãy đăng nhập bằng tài khoản mới.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function sendOtp(string $email, string $purpose): void
    {
        $code = str_pad((string) random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT);
        Cache::put(
            $this->otpCacheKey($purpose, $email),
            Hash::make($code),
            now()->addMinutes(10),
        );

        Mail::raw(
            "Mã xác minh Tixtak của bạn là: {$code}\nMã có hiệu lực trong 10 phút. Nếu bạn không yêu cầu mã này, hãy bỏ qua email.",
            function ($message) use ($email, $purpose): void {
                $subject = $purpose === 'registration' ? 'Mã xác minh đăng ký Tixtak' : 'Mã đặt lại mật khẩu Tixtak';
                $message->to($email)->subject($subject);
            },
        );
    }

    private function otpCacheKey(string $purpose, string $email): string
    {
        return 'tixtak:otp:'.$purpose.':'.hash('sha256', mb_strtolower($email));
    }
}
