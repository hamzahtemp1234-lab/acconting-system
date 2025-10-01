<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // ← إضافة هذه الاستيراد

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->withCount('roles');

        // البحث
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('Username', 'like', "%{$search}%");
            });
        }

        // التصفية حسب الدور
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('RoleName', $request->role);
            });
        }

        // التصفية حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('IsActive', $request->status == 'active');
        }

        $users = $query->paginate(10);
        $roles = Role::where('isActive', true)->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * عرض نموذج إضافة مستخدم
     */
    public function create()
    {
        $roles = Role::where('isActive', true)->get();
        $permissions = Permission::where('isActive', true)->get();
        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * حفظ المستخدم الجديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'Username' => 'nullable|string|max:50|unique:users,Username',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'IsActive' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // معالجة رفع الصورة
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            // إنشاء المستخدم
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'Username' => $validated['Username'] ?? null,
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'avatar' => $avatarPath,
                'IsActive' => $validated['IsActive'] ?? true,
            ]);

            // إضافة الأدوار
            $user->roles()->sync($validated['roles']);

            // إضافة الصلاحيات المباشرة
            if (isset($validated['permissions'])) {
                $user->permissions()->sync($validated['permissions']);
            }

            DB::commit();

            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'users',
                $user->id,
                'I',
                'إنشاء مستخدم جديد: ' . $user->name,
                Auth::id()
            );

            return redirect()->route('users.index')
                ->with('success', 'تم إضافة المستخدم بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            // حذف الصورة إذا فشل الإنشاء
            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
            return back()->with('error', 'حدث خطأ أثناء إضافة المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * عرض بيانات مستخدم
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('users.show', compact('user'));
    }

    /**
     * عرض نموذج تعديل مستخدم
     */
    public function edit(User $user)
    {
        $roles = Role::where('isActive', true)->get();
        $permissions = Permission::where('isActive', true)->get();
        $user->load('roles', 'roles.permissions');

        return view('users.create', compact('user', 'roles', 'permissions'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'Username' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'IsActive' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // تحديث البيانات الأساسية
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'Username' => $validated['Username'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'IsActive' => $validated['IsActive'] ?? $user->IsActive,
            ];

            // تحديث كلمة المرور إذا تم إدخالها
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            // معالجة رفع الصورة الجديدة
            if ($request->hasFile('avatar')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                // حفظ الصورة الجديدة
                $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($updateData);

            // تحديث الأدوار
            $user->roles()->sync($validated['roles']);

            // تحديث الصلاحيات المباشرة
            if (isset($validated['permissions'])) {
                $user->permissions()->sync($validated['permissions']);
            }

            DB::commit();

            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'users',
                $user->id,
                'U',
                'تحديث بيانات المستخدم: ' . $user->name,
                Auth::id()
            );

            return redirect()->route('users.index')
                ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * حذف مستخدم
     */
    public function destroy(User $user)
    {
        // منع المستخدم من حذف نفسه
        if ($user->id === Auth::id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الشخصي.');
        }

        DB::beginTransaction();
        try {
            // تسجيل قبل الحذف
            AuditTrailController::log(
                'users',
                $user->id,
                'D',
                'حذف المستخدم: ' . $user->name,
                Auth::id()
            );

            // حذف الصورة الشخصية إذا كانت موجودة
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // إزالة العلاقات أولاً
            $user->roles()->detach();
            $user->permissions()->detach();

            // الحذف النهائي أو Soft Delete
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'تم حذف المستخدم بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage());
        }
    }



    /**
     * تفعيل/تعطيل المستخدم
     */
    public function toggleStatus(User $user)
    {
        // منع المستخدم من تعطيل نفسه
        if ($user->id === Auth::id() && !$user->IsActive) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الشخصي.');
        }

        $user->update(['IsActive' => !$user->IsActive]);

        $status = $user->IsActive ? 'تفعيل' : 'تعطيل';

        // تسجيل مع المستخدم الحالي
        AuditTrailController::log(
            'users',
            $user->id,
            'U',
            $status . ' المستخدم: ' . $user->name,
            Auth::id() // ← إضافة المستخدم الحالي
        );

        return back()->with('success', "تم {$status} المستخدم بنجاح.");
    }

    /**
     * عرض سجل المستخدم
     */
    public function auditLog(User $user)
    {
        $auditLogs = AuditTrail::where('RecordID', $user->id)
            ->where('TableName', 'users')
            ->with('changedByUser')
            ->orderBy('ChangeDate', 'desc')
            ->paginate(10);

        return view('users.audit-log', compact('user', 'auditLogs'));
    }
    /**
     * حذف الصورة الشخصية
     */
    public function removeAvatar(User $user)
    {
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
                    'حذف الصورة الشخصية للمستخدم: ' . $user->name,
                    Auth::id()
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
