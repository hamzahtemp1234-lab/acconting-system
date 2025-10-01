@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-{{ isset($user) ? 'edit' : 'user-plus' }} ml-3 text-secondary"></i>
            {{ isset($user) ? 'تعديل مستخدم' : 'تسجيل مستخدم جديد' }}
        </h1>
        <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-4xl mx-auto bg-white p-10 rounded-xl shadow-2xl border-t-4 border-primary">

        <form id="userForm" action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($user))
            @method('PUT')
            @endif

            <!-- قسم معلومات تسجيل الدخول -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    معلومات تسجيل الدخول 🔑
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" required
                            value="{{ old('email', $user->email ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="example@company.com">
                        @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" required
                            value="{{ old('name', $user->name ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أدخل الاسم الرباعي">
                        @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="Username" class="block text-sm font-medium text-gray-700 mb-1">اسم المستخدم <span class="text-red-500">*</span></label>
                        <input id="Username" name="Username" type="text" required
                            value="{{ old('Username', $user->Username ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أدخل اسم المستخدم">
                        @error('Username')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            كلمة المرور <span class="text-red-500">*</span></label>
                        </label>
                        <input id="password" name="password" type="password"
                            {{ !isset($user) ? 'required' : '' }}
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="******">
                        @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            {{ !isset($user) ? 'required' : '' }}
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أعد إدخال كلمة المرور">
                    </div>
                </div>
            </div>

            <!-- قسم المعلومات الشخصية الجديدة -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    المعلومات الشخصية 👤
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- حقل الهاتف -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                        <input id="phone" name="phone" type="tel"
                            value="{{ old('phone', $user->phone ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="+966 5X XXX XXXX">
                        @error('phone')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- حقل العنوان -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition"
                            placeholder="أدخل العنوان الكامل">{{ old('address', $user->address ?? '') }}</textarea>
                        @error('address')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- حقل الصورة الشخصية -->
                    <div class="md:col-span-2">
                        <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">الصورة الشخصية</label>

                        <!-- معاينة الصورة الحالية -->
                        @if(isset($user) && $user->avatar)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">الصورة الحالية:</p>
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                    alt="صورة المستخدم"
                                    class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                                <button type="button"
                                    onclick="confirmRemoveAvatar()"
                                    class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                                    <i class="fas fa-trash ml-1"></i> حذف الصورة
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- زر رفع الصورة -->
                        <div class="flex items-center justify-center w-full">
                            <label for="avatar" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                    <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">انقر لرفع صورة</span> أو اسحبها هنا</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF (الحد الأقصى 2MB)</p>
                                </div>
                                <input id="avatar" name="avatar" type="file" class="hidden" accept="image/*" />
                            </label>
                        </div>

                        <!-- معاينة الصورة المختارة -->
                        <div id="avatarPreview" class="mt-3 hidden">
                            <p class="text-sm text-gray-600 mb-2">معاينة الصورة المختارة:</p>
                            <img id="previewImage" class="w-20 h-20 rounded-full object-cover border-2 border-secondary">
                        </div>

                        @error('avatar')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- قسم الدور والصلاحيات -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary border-r-4 border-secondary pr-3">
                    تحديد الدور والصلاحيات 🛡️
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="roles" class="block text-sm font-medium text-gray-700 mb-1">الدور/المسمى الوظيفي <span class="text-red-500">*</span></label>
                        <select id="roles" name="roles[]" multiple required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition bg-white">
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ (isset($user) && $user->roles->contains($role->id)) ? 'selected' : '' }}>
                                {{ $role->RoleName }}
                            </option>
                            @endforeach
                        </select>
                        @error('roles')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="IsActive" class="block text-sm font-medium text-gray-700 mb-1">حالة المستخدم</label>
                        <select id="IsActive" name="IsActive" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition bg-white">
                            <option value="1" {{ (isset($user) && $user->IsActive) ? 'selected' : '' }}>نشط (يمكنه تسجيل الدخول)</option>
                            <option value="0" {{ (isset($user) && !$user->IsActive) ? 'selected' : '' }}>غير نشط (معلق)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="flex justify-end space-x-4 space-x-reverse pt-4">
                <a href="{{ route('users.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                    <i class="fas fa-save ml-2"></i> {{ isset($user) ? 'تحديث' : 'حفظ' }} المستخدم
                </button>
            </div>
        </form>
    </div>
</main>

<!-- نموذج حذف الصورة -->
@if(isset($user) && $user->avatar)
<form id="removeAvatarForm" action="{{ route('users.remove-avatar', $user->id) }}" method="POST" class="hidden">
    @csrf
    @method('POST')
</form>
@endif

<script>
    // التحقق من تطابق كلمات المرور
    document.getElementById('userForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('كلمات المرور غير متطابقة!');
        }
    });

    // معاينة الصورة قبل الرفع
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('avatarPreview');
        const previewImage = document.getElementById('previewImage');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });

    // تأكيد حذف الصورة
    function confirmRemoveAvatar() {
        if (confirm('هل أنت متأكد من حذف الصورة الشخصية؟')) {
            document.getElementById('removeAvatarForm').submit();
        }
    }

    // تهيئة select2 للأدوار (اختياري)
    document.addEventListener('DOMContentLoaded', function() {
        // يمكنك إضافة تهيئة select2 هنا إذا كنت تستخدمها
        $('#roles').select2({
            placeholder: 'اختر الأدوار',
            allowClear: true
        });
    });
</script>

<style>
    /* تخصيص مظهر حقل رفع الملفات */
    #avatar:hover {
        border-color: #3b82f6;
    }

    /* تخصيص مظهر معاينة الصورة */
    #previewImage {
        transition: all 0.3s ease;
    }

    #previewImage:hover {
        transform: scale(1.1);
    }
</style>
@endsection