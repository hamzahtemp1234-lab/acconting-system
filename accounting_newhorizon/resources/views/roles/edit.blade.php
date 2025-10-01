@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل دور
        </h1>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-4xl mx-auto bg-white p-10 rounded-xl shadow-2xl border-t-4 border-primary">

        <form id="roleForm" action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

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
                            value="{{ old('RoleName', $role->RoleName) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أدخل اسم الدور">
                        @error('RoleName')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="Description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                        <textarea id="Description" name="Description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="وصف مختصر للدور ومسؤولياته...">{{ old('Description', $role->Description) }}</textarea>
                        @error('Description')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="isActive" class="block text-sm font-medium text-gray-700 mb-1">حالة الدور</label>
                        <select id="isActive" name="isActive"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition bg-white">
                            <option value="1" {{ old('isActive', $role->isActive) ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ !old('isActive', $role->isActive) ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- بديل أبسط بدون تصنيف -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    إدارة الصلاحيات المرتبطة بالدور 🔐
                </h3>

                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 ml-2"></i>
                        <p class="text-blue-700 text-sm">اختر الصلاحيات المناسبة لهذا الدور. سيتم إزالة الصلاحيات غير المحددة.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($permissions as $permission)
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            class="h-5 w-5 text-secondary rounded focus:ring-secondary"
                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                        <span class="mr-3 text-gray-700 font-medium">{{ $permission->PermissionName }}</span>
                        @if($permission->Description)
                        <span class="text-xs text-gray-500">({{ $permission->Description }})</span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-4 space-x-reverse">
                <a href="{{ route('roles.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                    <i class="fas fa-save ml-2"></i> تحديث الدور
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

    // التحقق من صحة النموذج قبل الإرسال
    document.getElementById('roleForm').addEventListener('submit', function(e) {
        const roleName = document.getElementById('RoleName').value.trim();
        if (!roleName) {
            e.preventDefault();
            alert('يرجى إدخال اسم الدور');
            return;
        }
    });
</script>
@endsection