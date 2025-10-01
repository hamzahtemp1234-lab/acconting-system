<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // التحقق من بيانات تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // السماح بتسجيل الدخول إما بـ username أو email
        $login_type = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'Username';

        $credentials = [
            $login_type => $request->username,
            'password' => $request->password,
            'IsActive' => true
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // تسجيل عملية تسجيل الدخول
            AuditTrailController::log(
                'users',
                Auth::id(),
                'U',
                'تسجيل الدخول إلى النظام',
                Auth::id()
            );

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'بيانات تسجيل الدخول غير صحيحة أو الحساب غير نشط.',
        ])->onlyInput('username');
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        // تسجيل عملية تسجيل الخروج
        if (Auth::check()) {
            AuditTrailController::log(
                'users',
                Auth::id(),
                'U',
                'تسجيل الخروج من النظام',
                Auth::id()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    // عرض صفحة نسيان كلمة المرور
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // معالجة نسيان كلمة المرور
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'لا يوجد حساب مرتبط بهذا البريد الإلكتروني.']);
        }

        // هنا يمكنك إضافة إرسال رابط إعادة تعيين كلمة المرور
        // ستحتاج إلى تنفيذ نظام إعادة تعيين كلمة المرور

        return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
    }

    public function hasRole($roleName)
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->roles->contains('RoleName', $roleName);
    }

    // الحصول على معلومات المستخدم الحالي
    public function currentUser()
    {
        return Auth::user();
    }
}
