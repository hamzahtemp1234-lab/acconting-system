<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * عرض نموذج طلب إعادة تعيين كلمة المرور
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * معالجة طلب إعادة تعيين كلمة المرور
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // البحث عن المستخدم بالبريد الإلكتروني
        $user = User::where('email', $request->email)->where('IsActive', true)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'لا يوجد حساب نشط مرتبط بهذا البريد الإلكتروني.']);
        }

        // إنشاء token لإعادة التعيين
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // إرسال البريد الإلكتروني (ستحتاج إلى تكوين إرسال البريد)
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'reset_url' => route('password.reset', $token)
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('إعادة تعيين كلمة المرور - نظام الإدارة');
            });

            return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'حدث خطأ أثناء إرسال البريد الإلكتروني. يرجى المحاولة لاحقاً.']);
        }
    }

    /**
     * عرض نموذج إعادة تعيين كلمة المرور
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * معالجة إعادة تعيين كلمة المرور
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // التحقق من صحة token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'رابط إعادة التعيين غير صالح أو منتهي الصلاحية.']);
        }

        // التحقق من أن token لم ينته صلاحيته (24 ساعة)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'رابط إعادة التعيين منتهي الصلاحية. يرجى طلب رابط جديد.']);
        }

        // تحديث كلمة المرور
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'المستخدم غير موجود.']);
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // حذف token المستخدم
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::commit();

            // تسجيل العملية
            AuditTrailController::log(
                'users',
                $user->id,
                'U',
                'إعادة تعيين كلمة المرور عبر رابط النسيان',
                $user->id
            );

            return redirect()->route('login')
                ->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => 'حدث خطأ أثناء إعادة تعيين كلمة المرور.']);
        }
    }
}
