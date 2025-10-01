<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * عرض قائمة الأدوار
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        if ($request->has('search') && $request->search != '') {
            $query->where('RoleName', 'like', "%{$request->search}%")
                ->orWhere('Description', 'like', "%{$request->search}%");
        }

        $roles = $query->paginate(10);
        return view('roles.index', compact('roles'));
    }

    /**
     * عرض نموذج إضافة دور
     */
    public function create()
    {
        $permissions = Permission::where('isActive', true)->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * حفظ الدور الجديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'RoleName' => 'required|string|max:50|unique:roles,RoleName',
            'Description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'isActive' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'RoleName' => $validated['RoleName'],
                'Description' => $validated['Description'],
                'isActive' => $validated['isActive'] ?? true,
            ]);

            // إضافة الصلاحيات إذا وجدت
            if (isset($validated['permissions'])) {
                $role->permissions()->sync($validated['permissions']);
            }

            DB::commit();

            AuditTrailController::log('roles', $role->id, 'I', 'إنشاء دور جديد: ' . $role->RoleName,   Auth::id());

            return redirect()->route('roles.index')
                ->with('success', 'تم إضافة الدور بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة الدور: ' . $e->getMessage());
        }
    }

    /**
     * عرض بيانات دور
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    /**
     * عرض نموذج تعديل دور
     */
    public function edit(Role $role)
    {
        $permissions = Permission::where('isActive', true)->get();
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * تحديث بيانات الدور
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'RoleName' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles')->ignore($role->id)
            ],
            'Description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'isActive' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'RoleName' => $validated['RoleName'],
                'Description' => $validated['Description'],
                'isActive' => $validated['isActive'] ?? $role->isActive,
            ]);

            // التصحيح: استخدام sync() مع array فارغ إذا لم يتم إرسال أي صلاحيات
            $permissions = $validated['permissions'] ?? [];
            $role->permissions()->sync($permissions);

            DB::commit();

            AuditTrailController::log('roles', $role->id, 'U', 'تحديث بيانات الدور: ' . $role->RoleName,  Auth::id());

            return redirect()->route('roles.index')
                ->with('success', 'تم تحديث الدور بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage());
        }
    }

    /**
     * إدارة صلاحيات الدور
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::where('isActive', true)->get();
        $role->load('permissions');

        // تصنيف الصلاحيات بناءً على الاسم (بدون حقل category)
        $permissionGroups = $this->categorizePermissions($permissions);

        return view('roles.permissions', compact('role', 'permissions', 'permissionGroups'));
    }

    /**
     * دالة مساعدة لتصنيف الصلاحيات تلقائياً بناءً على الاسم
     */
    private function categorizePermissions($permissions)
    {
        $groups = [
            'system' => [],
            'accounting' => [],
            'reports' => [],
            'general' => []
        ];

        foreach ($permissions as $permission) {
            $name = strtolower($permission->PermissionName);

            if (str_contains($name, 'system') || str_contains($name, 'user') || str_contains($name, 'role') || str_contains($name, 'permission')) {
                $groups['system'][] = $permission;
            } elseif (str_contains($name, 'account') || str_contains($name, 'financial') || str_contains($name, 'invoice') || str_contains($name, 'transaction')) {
                $groups['accounting'][] = $permission;
            } elseif (str_contains($name, 'report') || str_contains($name, 'analytics') || str_contains($name, 'statistic')) {
                $groups['reports'][] = $permission;
            } else {
                $groups['general'][] = $permission;
            }
        }

        return $groups;
    }

    /**
     * حذف دور
     */
    public function destroy(Role $role)
    {
        // منع حذف الأدوار التي لها مستخدمين
        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين.');
        }

        DB::beginTransaction();
        try {
            AuditTrailController::log('roles', $role->id, 'D', 'حذف الدور: ' . $role->RoleName,   Auth::id());

            // إزالة العلاقات أولاً
            $role->permissions()->detach();
            $role->users()->detach();

            $role->delete();

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'تم حذف الدور بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف الدور: ' . $e->getMessage());
        }
    }


    /** 
     * تحديث صلاحيات الدور
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            // التصحيح: استخدام array فارغ إذا لم يتم إرسال أي صلاحيات
            $permissions = $validated['permissions'] ?? [];
            $role->permissions()->sync($permissions);

            DB::commit();

            AuditTrailController::log('roles', $role->id, 'U', 'تحديث صلاحيات الدور: ' . $role->RoleName,   Auth::id());

            return redirect()->route('roles.index')
                ->with('success', 'تم تحديث صلاحيات الدور بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage());
        }
    }


    /**
     * تفعيل/تعطيل الدور
     */
    public function toggleStatus(Role $role)
    {
        $role->update(['isActive' => !$role->isActive]);

        $status = $role->isActive ? 'تفعيل' : 'تعطيل';
        AuditTrailController::log('roles', $role->id, 'U', $status . ' الدور: ' . $role->RoleName,   Auth::id());

        return back()->with('success', "تم {$status} الدور بنجاح.");
    }
}
