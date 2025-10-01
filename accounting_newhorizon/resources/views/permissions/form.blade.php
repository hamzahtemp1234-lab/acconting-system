@extends('layouts.app')

@section('content')
@php
$isEdit = isset($permission);
$title = $isEdit ? 'تعديل الصلاحية' : 'إضافة صلاحية جديدة';
$buttonText = $isEdit ? 'تحديث الصلاحية' : 'حفظ الصلاحية';
$icon = $isEdit ? 'fa-edit' : 'fa-key';
$formAction = $isEdit ? route('permissions.update', $permission->id) : route('permissions.store');
$method = $isEdit ? 'PUT' : 'POST';
@endphp

<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas {{ $icon }} ml-3 text-secondary"></i> {{ $title }}
        </h1>
        <a href="{{ route('permissions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-4xl mx-auto bg-white p-10 rounded-xl shadow-2xl border-t-4 border-primary">

        <form id="permissionForm" action="{{ $formAction }}" method="POST" class="space-y-8">
            @csrf
            @if($isEdit)
            @method('PUT')
            @endif

            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    المعلومات الأساسية للصلاحية 🔑
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label for="PermissionName" class="block text-sm font-medium text-gray-700 mb-1">
                            اسم الصلاحية <span class="text-red-500">*</span>
                        </label>
                        <input id="PermissionName" name="PermissionName" type="text" required
                            value="{{ old('PermissionName', $permission->PermissionName ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أدخل اسم الصلاحية (مثال: users.create, reports.view...)"
                            {{ $isEdit ? '' : 'autofocus' }}>
                        @error('PermissionName')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="Description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                        <textarea id="Description" name="Description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="وصف مختصر للصلاحية والوظائف التي تتيحها...">{{ old('Description', $permission->Description ?? '') }}</textarea>
                        @error('Description')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="isActive" class="block text-sm font-medium text-gray-700 mb-1">حالة الصلاحية</label>
                        <select id="isActive" name="isActive"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition bg-white">
                            <option value="1" {{ (old('isActive', $permission->isActive ?? 1) == 1) ? 'selected' : '' }}>نشط (يمكن استخدامه)</option>
                            <option value="0" {{ (old('isActive', $permission->isActive ?? 1) == 0) ? 'selected' : '' }}>غير نشط (معلق)</option>
                        </select>
                        @error('isActive')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($isEdit)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">معلومات النظام</label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">تاريخ الإنشاء:</span>
                                <span class="font-medium">{{ $permission->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">آخر تحديث:</span>
                                <span class="font-medium">{{ $permission->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                            @if($permission->roles_count > 0)
                            <div class="flex justify-between mt-2 pt-2 border-t border-gray-200">
                                <span class="text-gray-600">عدد الأدوار المرتبطة:</span>
                                <span class="font-medium text-primary">{{ $permission->roles_count }} دور</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="flex items-center justify-center">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 w-full">
                            <div class="flex items-center">
                                <i class="fas fa-lightbulb text-blue-500 ml-2"></i>
                                <p class="text-blue-700 text-sm">
                                    <strong>نصيحة:</strong> استخدم تسمية واضحة للصلاحية تشير إلى الوظيفة التي تتحكم فيها
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$isEdit)
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    أمثلة على تسميات الصلاحيات 💡
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- صلاحيات إدارة المستخدمين -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-primary mb-2">
                            <i class="fas fa-users ml-2 text-secondary"></i> إدارة المستخدمين
                        </h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• users.view</li>
                            <li>• users.create</li>
                            <li>• users.edit</li>
                            <li>• users.delete</li>
                            <li>• users.export</li>
                        </ul>
                    </div>

                    <!-- صلاحيات النظام -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-primary mb-2">
                            <i class="fas fa-cog ml-2 text-secondary"></i> إدارة النظام
                        </h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• settings.manage</li>
                            <li>• backup.manage</li>
                            <li>• logs.view</li>
                            <li>• system.monitor</li>
                            <li>• audit.view</li>
                        </ul>
                    </div>

                    <!-- صلاحيات التقارير -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-primary mb-2">
                            <i class="fas fa-chart-bar ml-2 text-secondary"></i> التقارير
                        </h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• reports.view</li>
                            <li>• reports.generate</li>
                            <li>• reports.export</li>
                            <li>• analytics.view</li>
                            <li>• dashboard.view</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @if($isEdit && $permission->roles_count > 0)
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    الأدوار المرتبطة بهذه الصلاحية 🛡️
                </h3>

                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 ml-2"></i>
                        <p class="text-yellow-700 text-sm">
                            <strong>ملاحظة:</strong> هذه الصلاحية مرتبطة بعدد {{ $permission->roles_count }} من الأدوار.
                            أي تغيير في هذه الصلاحية سيؤثر على جميع المستخدمين الذين لديهم هذه الأدوار.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="pt-4 flex justify-end space-x-4 space-x-reverse">
                <a href="{{ route('permissions.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                    <i class="fas fa-save ml-2"></i> {{ $buttonText }}
                </button>
            </div>
        </form>

        @if($isEdit)
        <!-- نموذج حذف الصلاحية -->
        <form id="deleteForm" action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        @endif

    </div>
</main>

<script>
    // التحقق من صحة النموذج قبل الإرسال
    document.getElementById('permissionForm').addEventListener('submit', function(e) {
        const permissionName = document.getElementById('PermissionName').value.trim();

        if (!permissionName) {
            e.preventDefault();
            alert('يرجى إدخال اسم الصلاحية');
            return;
        }

        // التحقق من تنسيق اسم الصلاحية (اختياري)
        if (!isValidPermissionName(permissionName)) {
            e.preventDefault();
            alert('يرجى استخدام تنسيق مناسب لاسم الصلاحية (مثال: module.action)');
            return;
        }
    });

    // دالة للتحقق من تنسيق اسم الصلاحية
    function isValidPermissionName(name) {
        // يمكن تعديل هذا التحقق حسب احتياجاتك
        const regex = /^[a-z_]+\.[a-z_]+$/;
        return regex.test(name);
    }

    // عرض معاينة للصلاحية قبل الحفظ
    document.getElementById('PermissionName').addEventListener('input', function() {
        const preview = document.getElementById('permissionPreview');
        if (!preview) {
            const previewDiv = document.createElement('div');
            previewDiv.id = 'permissionPreview';
            previewDiv.className = 'mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200';
            previewDiv.innerHTML = `<strong>معاينة:</strong> سيتم {{ $isEdit ? 'تحديث' : 'إنشاء' }} صلاحية باسم "<span class="text-primary">${this.value}</span>"`;
            this.parentNode.appendChild(previewDiv);
        } else {
            preview.innerHTML = `<strong>معاينة:</strong> سيتم {{ $isEdit ? 'تحديث' : 'إنشاء' }} صلاحية باسم "<span class="text-primary">${this.value}</span>"`;
        }
    });

    // إضافة نص توجيهي عند التركيز على حقل اسم الصلاحية
    document.getElementById('PermissionName').addEventListener('focus', function() {
        const hint = document.getElementById('permissionHint');
        if (!hint) {
            const hintDiv = document.createElement('div');
            hintDiv.id = 'permissionHint';
            hintDiv.className = 'mt-1 text-xs text-gray-500';
            hintDiv.innerHTML = 'يفضل استخدام تنسيق module.action (مثال: users.create, reports.view)';
            this.parentNode.appendChild(hintDiv);
        }
    });

    @if($isEdit)
    // تأكيد حذف الصلاحية
    function confirmDelete() {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                @if($permission['roles_count'] > 0)
                Swal.fire({
                    title: 'لا يمكن الحذف',
                    text: 'لا يمكن حذف الصلاحية لأنها مرتبطة بأدوار. يرجى إزالة الارتباطات أولاً.',
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
                @else
                document.getElementById('deleteForm').submit();
                @endif
            }
        });
    }
    @endif

    // تهيئة الحقول عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const permissionName = document.getElementById('PermissionName').value;
        if (permissionName) {
            const event = new Event('input');
            document.getElementById('PermissionName').dispatchEvent(event);
        }
    });
</script>

<style>
    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .grid.grid-cols-1.md\\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3 {
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

    /* تنسيقات للعناوين */
    h3.text-xl.font-semibold {
        color: var(--primary-color);
        border-color: var(--secondary-color);
    }

    /* تنسيقات للبطاقات التوضيحية */
    .bg-gray-50 {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .bg-gray-50:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* تحسينات للحقول */
    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        ring: 2px;
        ring-color: var(--secondary-color);
    }
</style>
@endsection