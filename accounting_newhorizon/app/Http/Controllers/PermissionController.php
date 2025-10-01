<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * عرض قائمة الصلاحيات
     */
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        if ($request->has('search') && $request->search != '') {
            $query->where('PermissionName', 'like', "%{$request->search}%")
                ->orWhere('Description', 'like', "%{$request->search}%");
        }

        $permissions = $query->paginate(15);
        return view('permissions.index', compact('permissions'));
    }

    /**
     * عرض نموذج إضافة صلاحية
     */
    public function create()
    {
        return view('permissions.form');
    }

    /**
     * حفظ الصلاحية الجديدة
     */
    /**
     * حفظ الصلاحية الجديدة (الطريقة البديلة)
     */
    public function store(Request $request)
    {
        // التحقق أولاً إذا كانت هناك صلاحية محذوفة بنفس الاسم
        $deletedPermission = Permission::withTrashed()
            ->where('PermissionName', $request->PermissionName)
            ->whereNotNull('deleted_at')
            ->first();

        if ($deletedPermission) {
            return back()->with('error', 'هذا الاسم مستخدم سابقاً في صلاحية محذوفة. يرجى استخدام اسم مختلف.')
                ->withInput();
        }

        $validated = $request->validate([
            'PermissionName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions')->whereNull('deleted_at')
            ],
            'Description' => 'nullable|string|max:255',
            'isActive' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $permission = Permission::create([
                'PermissionName' => $validated['PermissionName'],
                'Description' => $validated['Description'],
                'isActive' => $validated['isActive'] ?? true,
            ]);

            DB::commit();

            AuditTrailController::log('permissions', $permission->id, 'I', 'إنشاء صلاحية جديدة: ' . $permission->PermissionName,   Auth::id());

            return redirect()->route('permissions.index')
                ->with('success', 'تم إضافة الصلاحية بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة الصلاحية: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض بيانات صلاحية
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }

    /**
     * عرض نموذج تعديل صلاحية
     */
    public function edit(Permission $permission)
    {
        return view('permissions.form', compact('permission'));
    }

    /**
     * تحديث بيانات الصلاحية
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'PermissionName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions')->ignore($permission->id)
            ],
            'Description' => 'nullable|string|max:255',
            'isActive' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $permission->update([
                'PermissionName' => $validated['PermissionName'],
                'Description' => $validated['Description'],
                'isActive' => $validated['isActive'] ?? $permission->isActive,
            ]);

            DB::commit();

            AuditTrailController::log('permissions', $permission->id, 'U', 'تحديث بيانات الصلاحية: ' . $permission->PermissionName,   Auth::id());

            return redirect()->route('permissions.index')
                ->with('success', 'تم تحديث الصلاحية بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * حذف صلاحية
     */ /**
     * حذف صلاحية
     */
    public function destroy(Permission $permission)
    {
        // منع حذف الصلاحيات المرتبطة بأدوار
        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الصلاحية لأنها مرتبطة بأدوار.');
        }

        DB::beginTransaction();
        try {
            AuditTrailController::log('permissions', $permission->id, 'D', 'حذف الصلاحية: ' . $permission->PermissionName,   Auth::id());

            $permission->delete();

            DB::commit();

            return redirect()->route('permissions.index')
                ->with('success', 'تم حذف الصلاحية بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * تفعيل/تعطيل الصلاحية
     */
    public function toggleStatus(Permission $permission)
    {
        $permission->update(['isActive' => !$permission->isActive]);

        $status = $permission->isActive ? 'تفعيل' : 'تعطيل';
        AuditTrailController::log('permissions', $permission->id, 'U', $status . ' الصلاحية: ' . $permission->PermissionName,   Auth::id());

        return back()->with('success', "تم {$status} الصلاحية بنجاح.");
    }
}
