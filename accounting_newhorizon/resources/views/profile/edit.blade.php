@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل الملف الشخصي
        </h1>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
                <i class="fas fa-arrow-right ml-2"></i> العودة للبروفايل
            </a>
        </div>
    </header>

    <div class="max-w-4xl mx-auto">
        <!-- بطاقة تعديل الملف الشخصي -->
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8 mb-6">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- قسم المعلومات الأساسية -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-xl font-semibold mb-6 text-primary border-r-4 border-secondary pr-3">
                        <i class="fas fa-user-circle ml-2 text-secondary"></i> المعلومات الأساسية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- الاسم الكامل -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                الاسم الكامل <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-200"
                                placeholder="أدخل الاسم الكامل" required>
                            @error('name')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                البريد الإلكتروني <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-200"
                                placeholder="example@company.com" required>
                            @error('email')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- اسم المستخدم -->
                        <div>
                            <label for="Username" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم المستخدم <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="Username" name="Username" value="{{ old('Username', $user->Username) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-200"
                                placeholder="أدخل اسم المستخدم" required>
                            @error('Username')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- رقم الهاتف -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم الهاتف
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-200"
                                placeholder="+966 5X XXX XXXX">
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- العنوان -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                العنوان
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-200 resize-none"
                                placeholder="أدخل العنوان الكامل">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- قسم الصورة الشخصية -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-xl font-semibold mb-6 text-primary border-r-4 border-secondary pr-3">
                        <i class="fas fa-camera ml-2 text-secondary"></i> الصورة الشخصية
                    </h3>

                    <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8 space-x-reverse">
                        <!-- معاينة الصورة الحالية -->
                        <div class="text-center">
                            @if($user->avatar)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="الصورة الحالية"
                                    class="w-32 h-32 rounded-full border-4 border-secondary shadow-lg object-cover">
                                <!-- زر حذف الصورة -->
                                <form action="{{ route('profile.remove-avatar') }}" method="POST" class="absolute -top-2 -right-2">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                        onclick="return confirm('هل أنت متأكد من حذف الصورة الشخصية؟')"
                                        class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition shadow-lg">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">الصورة الحالية</p>
                            @else
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-secondary to-primary flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="text-white text-3xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">لا توجد صورة</p>
                            @endif
                        </div>

                        <!-- رفع صورة جديدة -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                رفع صورة جديدة
                            </label>

                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- منطقة سحب وإفلات -->
                                <label for="avatar" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">انقر لرفع صورة</span> أو اسحبها هنا
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF - الحد الأقصى 2MB</p>
                                    </div>
                                    <input id="avatar" name="avatar" type="file" class="hidden" accept="image/*" />
                                </label>

                                <!-- معاينة الصورة المختارة -->
                                <div id="avatarPreview" class="hidden text-center">
                                    <p class="text-sm font-medium text-gray-700 mb-2">معاينة الصورة الجديدة:</p>
                                    <img id="previewImage" class="w-20 h-20 rounded-full border-2 border-secondary object-cover mx-auto shadow-md">
                                </div>

                                @error('avatar')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات إضافية (للقراءة فقط) -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle ml-2 text-secondary"></i> معلومات النظام
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                            <span class="text-gray-600">تاريخ الانضمام:</span>
                            <span class="font-medium text-primary">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                            <span class="text-gray-600">آخر تحديث:</span>
                            <span class="font-medium text-primary">{{ $user->updated_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                            <span class="text-gray-600">حالة الحساب:</span>
                            <span class="font-medium {{ $user->IsActive ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->IsActive ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 space-x-reverse pt-6">
                    <a href="{{ route('profile.show') }}" class="px-8 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-center">
                        <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                    <button type="submit" class="px-8 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30 flex items-center justify-center">
                        <i class="fas fa-save ml-2"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>

        <!-- بطاقة الإجراءات السريعة -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- تغيير كلمة المرور -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover-lift transition duration-200">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-key text-blue-600 text-xl"></i>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-lg font-semibold text-gray-800">كلمة المرور</h3>
                        <p class="text-sm text-gray-600">تحديث كلمة المرور الخاصة بك</p>
                    </div>
                </div>
                <a href="{{ route('profile.change-password') }}" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium text-center block">
                    <i class="fas fa-edit ml-2"></i> تغيير كلمة المرور
                </a>
            </div>

            <!-- سجل النشاطات -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover-lift transition duration-200">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-history text-green-600 text-xl"></i>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-lg font-semibold text-gray-800">سجل النشاطات</h3>
                        <p class="text-sm text-gray-600">عرض سجل نشاطاتك في النظام</p>
                    </div>
                </div>
                <a href="{{ route('profile.activity-log') }}" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium text-center block">
                    <i class="fas fa-list ml-2"></i> عرض السجل
                </a>
            </div>
        </div>
    </div>
</main>

<script>
    // معاينة الصورة قبل الرفع
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('avatarPreview');
        const previewImage = document.getElementById('previewImage');

        if (file) {
            // التحقق من حجم الملف (2MB كحد أقصى)
            if (file.size > 2 * 1024 * 1024) {
                alert('حجم الملف كبير جداً! الحد الأقصى المسموح به هو 2MB.');
                this.value = '';
                return;
            }

            // التحقق من نوع الملف
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('نوع الملف غير مدعوم! يرجى رفع صورة بصيغة JPG, PNG, أو GIF.');
                this.value = '';
                return;
            }

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

    // تأثيرات تفاعلية لحقل رفع الملفات
    const dropArea = document.querySelector('label[for="avatar"]');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('border-secondary', 'bg-blue-50');
    }

    function unhighlight() {
        dropArea.classList.remove('border-secondary', 'bg-blue-50');
    }

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('avatar').files = files;

        // تشغيل حدث change يدوياً
        const event = new Event('change');
        document.getElementById('avatar').dispatchEvent(event);
    }

    // التحقق من صحة البيانات قبل الإرسال
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const username = document.getElementById('Username').value.trim();

        if (!name || !email || !username) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول الإلزامية');
            return;
        }

        // التحقق من صيغة البريد الإلكتروني
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('يرجى إدخال بريد إلكتروني صحيح');
            return;
        }
    });
</script>

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    /* تخصيص شريط التمرير */
    textarea::-webkit-scrollbar {
        width: 6px;
    }

    textarea::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    textarea::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    textarea::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* تأثيرات على حقول الإدخال */
    input:focus,
    textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>
@endsection