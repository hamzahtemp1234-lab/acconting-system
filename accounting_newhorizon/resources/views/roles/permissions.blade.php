@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-shield-alt ml-3 text-secondary"></i> إدارة صلاحيات الدور: {{ $role->RoleName }}
        </h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
                <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
            </a>
            <a href="{{ route('roles.edit', $role->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                <i class="fas fa-edit ml-2"></i> تعديل الدور
            </a>
        </div>
    </header>

    <div class="max-w-6xl mx-auto bg-white p-10 rounded-xl shadow-2xl border-t-4 border-primary">

        <form id="permissionsForm" action="{{ route('roles.permissions.update', $role->id) }}" method="POST" class="space-y-8">
            @csrf

            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 ml-2 text-xl"></i>
                    <div class="mr-3">
                        <p class="text-blue-700 font-semibold">معلومات الدور</p>
                        <p class="text-blue-600 text-sm">اسم الدور: <span class="font-bold">{{ $role->RoleName }}</span> | الوصف: {{ $role->Description ?? 'لا يوجد وصف' }}</p>
                        <p class="text-blue-600 text-sm">عدد المستخدمين: <span class="font-bold">{{ $role->users_count }}</span> مستخدم</p>
                    </div>
                </div>
            </div>

            <!-- عرض الصلاحيات حسب الفئات -->
            @foreach($permissionGroups as $category => $permissions)
            @if(count($permissions) > 0)
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-b pb-2">
                    <i class="fas fa-{{ $category == 'system' ? 'cogs' : ($category == 'accounting' ? 'chart-line' : ($category == 'clients' ? 'users' : ($category == 'reports' ? 'chart-bar' : 'shield-alt'))) }} ml-2 text-secondary"></i>
                    {{ $category == 'system' ? 'صلاحيات إدارة النظام' : ($category == 'accounting' ? 'صلاحيات المحاسبة المالية' : ($category == 'clients' ? 'صلاحيات العملاء والموردين' : ($category == 'reports' ? 'صلاحيات التقارير والإحصائيات' : 'صلاحيات عامة'))) }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer border border-gray-200">
                        <input type="checkbox" name="permisszions[]" value="{{ $permission->id }}"
                            class="h-5 w-5 text-secondary rounded focus:ring-secondary"
                            {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                        <div class="mr-3">
                            <span class="text-gray-700 font-medium block">{{ $permission->PermissionName }}</span>
                            @if($permission->Description)
                            <span class="text-gray-500 text-xs block mt-1">{{ $permission->Description }}</span>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach

            <!-- أزرار التحكم السريع -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-lg font-semibold text-primary mb-3">أدوات التحكم السريع:</h4>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="selectAllPermissions()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-check-double ml-1"></i> تحديد الكل
                    </button>
                    <button type="button" onclick="deselectAllPermissions()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-times ml-1"></i> إلغاء الكل
                    </button>
                </div>
            </div>

            @error('permissions')
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <span class="text-red-600 font-medium"><i class="fas fa-exclamation-triangle ml-2"></i> {{ $message }}</span>
            </div>
            @enderror

            <div class="pt-4 flex justify-end space-x-4 space-x-reverse">
                <a href="{{ route('roles.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                    <i class="fas fa-save ml-2"></i> حفظ الصلاحيات
                </button>
            </div>
        </form>

    </div>
</main>

<script>
    // وظائف التحكم في الصلاحيات
    function selectAllPermissions() {
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        showToast('تم تحديد جميع الصلاحيات', 'success');
    }

    function deselectAllPermissions() {
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        showToast('تم إلغاء جميع الصلاحيات', 'warning');
    }

    // دالة لعرض الإشعارات
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 p-4 rounded-lg text-white font-medium z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'warning' ? 'bg-yellow-500' : 
            'bg-blue-500'
        }`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : 'info-circle'} ml-2"></i>
            ${message}
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // التحقق من صحة النموذج قبل الإرسال
    document.getElementById('permissionsForm').addEventListener('submit', function(e) {
        const checkedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked').length;

        if (checkedPermissions === 0) {
            e.preventDefault();
            if (!confirm('لم يتم اختيار أي صلاحيات. هل تريد الاستمرار بدون إضافة صلاحيات لهذا الدور؟')) {
                return;
            }
        }
    });
</script>
@endsection