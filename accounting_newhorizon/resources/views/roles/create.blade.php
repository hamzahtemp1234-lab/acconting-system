@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-3 text-secondary"></i> إضافة دور جديد
        </h1>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-4xl mx-auto bg-white p-10 rounded-xl shadow-2xl border-t-4 border-primary">

        <form id="roleForm" action="{{ route('roles.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    المعلومات الأساسية للدور 🛡️
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label for="RoleName" class="block text-sm font-medium text-gray-700 mb-1">
                            اسم الدور <span class="text-red-500">*</span>
                        </label>
                        <input id="RoleName" name="RoleName" type="text" required
                            value="{{ old('RoleName') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أدخل اسم الدور (مثال: مدير النظام، محاسب رئيسي...)">
                        @error('RoleName')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="Description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                        <textarea id="Description" name="Description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="وصف مختصر للدور ومسؤولياته...">{{ old('Description') }}</textarea>
                        @error('Description')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="isActive" class="block text-sm font-medium text-gray-700 mb-1">حالة الدور</label>
                        <select id="isActive" name="isActive"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition bg-white">
                            <option value="1" {{ old('isActive', 1) ? 'selected' : '' }}>نشط (يمكن استخدامه)</option>
                            <option value="0" {{ !old('isActive', 1) ? 'selected' : '' }}>غير نشط (معلق)</option>
                        </select>
                        @error('isActive')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    إدارة الصلاحيات المرتبطة بالدور 🔐
                </h3>

                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 ml-2"></i>
                        <p class="text-blue-700 text-sm">اختر الصلاحيات المناسبة لهذا الدور. يمكن للمستخدمين الذين يحملون هذا الدور الوصول إلى الوظائف المحددة.</p>
                    </div>
                </div>

                <!-- مجموعة صلاحيات إدارة النظام -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-primary mb-3 border-b pb-2">
                        <i class="fas fa-cogs ml-2 text-secondary"></i> صلاحيات إدارة النظام
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                        @foreach($permissions as $key)
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $key.PermissionName }}"
                                class="h-5 w-5 text-secondary rounded focus:ring-secondary"
                                {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                            <span class="mr-3 text-gray-700 font-medium">{{ $key.PermissionName }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>


                @error('permissions')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 flex justify-end space-x-4 space-x-reverse">
                <a href="{{ route('roles.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                    <i class="fas fa-save ml-2"></i> حفظ الدور
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
    }

    function deselectAllPermissions() {
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function selectSystemPermissions() {
        deselectAllPermissions();
        const systemKeys = ['users_manage', 'roles_manage', 'permissions_manage', 'system_settings', 'backup_manage', 'logs_view'];
        systemKeys.forEach(key => {
            const checkbox = document.querySelector(`input[value="${key}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    function selectAccountingPermissions() {
        deselectAllPermissions();
        const accountingKeys = ['financial_reports_view', 'financial_reports_export', 'transactions_manage', 'invoices_manage', 'accounts_manage', 'budget_manage'];
        accountingKeys.forEach(key => {
            const checkbox = document.querySelector(`input[value="${key}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    // التحقق من صحة النموذج قبل الإرسال
    document.getElementById('roleForm').addEventListener('submit', function(e) {
        const roleName = document.getElementById('RoleName').value.trim();
        if (!roleName) {
            e.preventDefault();
            alert('يرجى إدخال اسم الدور');
            return;
        }

        // يمكن إضافة المزيد من التحقق هنا إذا لزم الأمر
    });

    // عرض معاينة للدور قبل الحفظ
    document.getElementById('RoleName').addEventListener('input', function() {
        const preview = document.getElementById('rolePreview');
        if (!preview) {
            const previewDiv = document.createElement('div');
            previewDiv.id = 'rolePreview';
            previewDiv.className = 'mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200';
            previewDiv.innerHTML = `<strong>معاينة:</strong> سيتم إنشاء دور باسم "<span class="text-primary">${this.value}</span>"`;
            this.parentNode.appendChild(previewDiv);
        } else {
            preview.innerHTML = `<strong>معاينة:</strong> سيتم إنشاء دور باسم "<span class="text-primary">${this.value}</span>"`;
        }
    });
</script>

<style>
    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .grid.grid-cols-1.md\\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .flex.space-x-4.space-x-reverse {
            flex-direction: column-reverse;
            gap: 1rem;
        }

        .flex.space-x-4.space-x-reverse button,
        .flex.space-x-4.space-x-reverse a {
            width: 100%;
            text-align: center;
        }
    }

    /* تحسينات لل checkboxes */
    input[type="checkbox"] {
        transform: scale(1.1);
    }

    label:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* تنسيقات للعناوين */
    h4.text-lg.font-semibold {
        color: var(--primary-color);
        border-color: var(--secondary-color);
    }
</style>
@endsection