<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * عرض صفحة البروفايل
     */
    public function show()
    {
        $user = Auth::user();
        //$user->load('roles.permissions');

        return view('profile.show', compact('user'));
    }

    /**
     * عرض نموذج تعديل البروفايل
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث بيانات البروفايل
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'Username' => 'nullable|string|max:50|unique:users,Username,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'Username' => $validated['Username'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ];

            // معالجة صورة البروفايل
            if ($request->hasFile('avatar')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar); // ← تصحيح هنا
                }

                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $updateData['avatar'] = $avatarPath;
            }

            $user->update($updateData); // ← إلغاء التعليق

            DB::commit();

            AuditTrailController::log(
                'users',
                $user->id,
                'U',
                'تحديث بيانات البروفايل',
                Auth::id()
            );

            return redirect()->route('profile.show')
                ->with('success', 'تم تحديث بيانات البروفايل بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث البروفايل: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تغيير كلمة المرور
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'كلمة المرور الحالية غير صحيحة.');
        }

        DB::beginTransaction();
        try {
            $user->update([ // ← إلغاء التعليق
                'password' => Hash::make($validated['password'])
            ]);

            DB::commit();

            AuditTrailController::log(
                'users',
                $user->id,
                'U',
                'تغيير كلمة المرور',
                Auth::id()
            );

            return redirect()->route('profile.show')
                ->with('success', 'تم تغيير كلمة المرور بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تغيير كلمة المرور: ' . $e->getMessage());
        }
    }

    /**
     * عرض سجل النشاطات
     */

    // في دالة activityLog في ProfileController
    public function activityLog(Request $request)
    {
        $user = Auth::user();

        $query = AuditTrail::where('ChangedBy', $user->id)
            ->with('changedByUser')
            ->orderBy('ChangeDate', 'desc');

        // تطبيق الفلاتر
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('ChangeDescription', 'like', "%{$request->search}%")
                    ->orWhere('Details', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('table') && $request->table != '') {
            $query->where('TableName', $request->table);
        }

        if ($request->has('change_type') && $request->change_type != '') {
            $query->where('ChangeType', $request->change_type);
        }

        // فلترة الفترة الزمنية
        if ($request->has('period') && $request->period != '') {
            $date = now();
            switch ($request->period) {
                case 'today':
                    $query->whereDate('ChangeDate', $date->toDateString());
                    break;
                case 'week':
                    $query->where('ChangeDate', '>=', $date->subWeek());
                    break;
                case 'month':
                    $query->where('ChangeDate', '>=', $date->subMonth());
                    break;
                case 'year':
                    $query->where('ChangeDate', '>=', $date->subYear());
                    break;
            }
        }

        $auditLogs = $query->paginate(15);

        // إحصائيات المستخدم
        $userStats = [
            'inserts' => AuditTrail::where('ChangedBy', $user->id)->where('ChangeType', 'I')->count(),
            'updates' => AuditTrail::where('ChangedBy', $user->id)->where('ChangeType', 'U')->count(),
            'deletes' => AuditTrail::where('ChangedBy', $user->id)->where('ChangeType', 'D')->count(),
        ];

        // الجداول التي تفاعل معها المستخدم
        $userTables = AuditTrail::where('ChangedBy', $user->id)
            ->distinct()
            ->pluck('TableName');

        // أكثر الجداول تفاعلاً
        $topTables = AuditTrail::where('ChangedBy', $user->id)
            ->select('TableName', DB::raw('count(*) as count'))
            ->groupBy('TableName')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->pluck('count', 'TableName');

        // النشاط خلال الأسبوع
        $weeklyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->translatedFormat('l');
            $count = AuditTrail::where('ChangedBy', $user->id)
                ->whereDate('ChangeDate', $date->toDateString())
                ->count();
            $weeklyActivity[$dayName] = $count;
        }

        // آخر نشاط
        $lastActivity = AuditTrail::where('ChangedBy', $user->id)
            ->orderBy('ChangeDate', 'desc')
            ->value('ChangeDate');

        return view('profile.activity-log', compact(
            'user',
            'auditLogs',
            'userStats',
            'userTables',
            'topTables',
            'weeklyActivity',
            'lastActivity'
        ));
    }
    /**
     * حذف الصورة الشخصية للبروفايل - دالة جديدة
     */
    public function removeAvatar(Request $request)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            if ($user->avatar) {
                // حذف الصورة من التخزين
                Storage::disk('public')->delete($user->avatar);

                // تحديث قاعدة البيانات
                $user->update(['avatar' => null]);

                // تسجيل في سجل التدقيق
                AuditTrailController::log(
                    'users',
                    $user->id,
                    'U',
                    'حذف الصورة الشخصية من البروفايل',
                    $user->id
                );

                DB::commit();
                return back()->with('success', 'تم حذف الصورة الشخصية بنجاح.');
            }

            return back()->with('info', 'لا توجد صورة شخصية لحذفها.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف الصورة: ' . $e->getMessage());
        }
    }
}
