@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-cogs ml-3 text-secondary"></i> إعداد النظام
        </h1>
        <div class="text-sm text-gray-500">
            الخطوة 1 من 1
        </div>
    </header>

    <!-- بطاقة التقدم -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">إعداد النظام الأولي</h3>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    جاهز للإعداد
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-secondary h-2 rounded-full w-full"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">أكمل الإعدادات الأساسية لبدء استخدام النظام</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
            <form action="{{ route('system-settings.store') }}" method="POST" enctype="multipart/form-data" id="setupForm">
                @csrf

                <!-- قسم معلومات الشركة -->
                <div class="border-b border-gray-200 pb-8 mb-8">
                    <h3 class="text-xl font-semibold mb-6 text-primary border-r-4 border-secondary pr-3">
                        <i class="fas fa-building ml-2 text-secondary"></i> معلومات الشركة الأساسية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- اسم الشركة -->
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم الشركة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="company_name" name="company_name"
                                value="{{ old('company_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                                placeholder="أدخل اسم الشركة الرسمي"
                                required>
                            @error('company_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- شعار الشركة -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                شعار الشركة
                            </label>
                            <div class="flex flex-col items-center space-y-4">
                                <!-- منطقة رفع الشعار -->
                                <label for="logo" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">انقر لرفع الشعار</span> أو اسحبه هنا
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF - الحد الأقصى 2MB</p>
                                    </div>
                                    <input id="logo" name="logo" type="file" class="hidden" accept="image/*" />
                                </label>

                                <!-- معاينة الشعار -->
                                <div id="logoPreview" class="hidden text-center">
                                    <p class="text-sm font-medium text-gray-700 mb-2">معاينة الشعار:</p>
                                    <img id="previewLogo" class="w-32 h-32 rounded-lg border-2 border-secondary object-cover mx-auto shadow-md">
                                </div>
                            </div>
                            @error('logo')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                البريد الإلكتروني
                            </label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                                placeholder="company@example.com">
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- رقم الهاتف -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم الهاتف
                            </label>
                            <input type="tel" id="phone" name="phone"
                                value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                                placeholder="+966 5X XXX XXXX">
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- العنوان -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                العنوان
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition resize-none"
                                placeholder="أدخل العنوان الكامل للشركة">{{ old('address') }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- قسم الإعدادات المالية -->
                <div class="border-b border-gray-200 pb-8 mb-8">
                    <h3 class="text-xl font-semibold mb-6 text-primary border-r-4 border-secondary pr-3">
                        <i class="fas fa-chart-line ml-2 text-secondary"></i> الإعدادات المالية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- بداية السنة المالية -->
                        <div>
                            <label for="fiscal_start_month" class="block text-sm font-medium text-gray-700 mb-2">
                                بداية السنة المالية <span class="text-red-500">*</span>
                            </label>
                            <select id="fiscal_start_month" name="fiscal_start_month"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition" required>
                                <option value="">اختر الشهر</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('fiscal_start_month') == $i ? 'selected' : '' }}>
                                    الشهر {{ $i }} - {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                    @endfor
                            </select>
                            <p class="text-xs text-gray-500 mt-1">الشهر الذي تبدأ فيه السنة المالية</p>
                            @error('fiscal_start_month')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- العملة الافتراضية -->
                        <div>
                            <label for="default_currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                                العملة الافتراضية <span class="text-red-500">*</span>
                            </label>
                            <select id="default_currency_id" name="default_currency_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition" required>
                                <option value="">اختر العملة</option>
                                @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('default_currency_id') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->name }} ({{ $currency->code }})
                                </option>
                                @endforeach
                            </select>

                            @error('default_currency_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- المنازل العشرية -->
                        <div>
                            <label for="decimal_places" class="block text-sm font-medium text-gray-700 mb-2">
                                المنازل العشرية <span class="text-red-500">*</span>
                            </label>
                            <select id="decimal_places" name="decimal_places"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition" required>
                                <option value="">اختر عدد المنازل</option>
                                @for($i = 0; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('decimal_places') == $i ? 'selected' : '' }}>
                                    {{ $i }} منزلة
                                    </option>
                                    @endfor
                            </select>
                            <p class="text-xs text-gray-500 mt-1">عدد المنازل العشرية في العمليات الحسابية</p>
                            @error('decimal_places')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ملخص الإعدادات -->
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200 mb-8">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4">
                        <i class="fas fa-info-circle ml-2"></i> ملخص الإعدادات
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>اسم الشركة:</strong>
                            <span id="summaryCompany">---</span>
                        </div>
                        <div>
                            <strong>بداية السنة المالية:</strong>
                            <span id="summaryFiscal">---</span>
                        </div>
                        <div>
                            <strong>العملة الافتراضية:</strong>
                            <span id="summaryCurrency">---</span>
                        </div>
                        <div>
                            <strong>المنازل العشرية:</strong>
                            <span id="summaryDecimal">---</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500 mb-4 sm:mb-0">
                        <i class="fas fa-lightbulb ml-1"></i>
                        يمكنك تعديل هذه الإعدادات لاحقاً من لوحة التحكم
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 space-x-reverse">
                        <a href="{{ url('/') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-center">
                            <i class="fas fa-times ml-2"></i> إلغاء
                        </a>
                        <button type="submit" class="px-8 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30 flex items-center justify-center">
                            <i class="fas fa-check ml-2"></i> إنهاء الإعداد وبدء الاستخدام
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- معلومات المساعدة -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">معلومات الشركة</h4>
                <p class="text-sm text-gray-600">سيتم استخدام هذه المعلومات في الفواتير والتقارير</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">إعدادات مالية</h4>
                <p class="text-sm text-gray-600">تؤثر على العمليات الحسابية والتقارير المالية</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-cog text-purple-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">قابلة للتعديل</h4>
                <p class="text-sm text-gray-600">يمكنك تعديل جميع الإعدادات لاحقاً حسب الحاجة</p>
            </div>
        </div>
    </div>
</main>

<!-- نموذج التحميل -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-cog fa-spin text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">جاري إعداد النظام</h3>
            <div class="mt-4">
                <p class="text-sm text-gray-500">يرجى الانتظار أثناء إعداد النظام...</p>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-secondary h-2 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // معاينة الشعار
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logoPreview');
        const previewImage = document.getElementById('previewLogo');

        if (file) {
            // التحقق من حجم الملف
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

    // تحديث الملخص
    function updateSummary() {
        document.getElementById('summaryCompany').textContent =
            document.getElementById('company_name').value || '---';

        const fiscalSelect = document.getElementById('fiscal_start_month');
        const fiscalText = fiscalSelect.options[fiscalSelect.selectedIndex]?.text || '---';
        document.getElementById('summaryFiscal').textContent = fiscalText;

        const currencySelect = document.getElementById('default_currency_id');
        const currencyText = currencySelect.options[currencySelect.selectedIndex]?.text || '---';
        document.getElementById('summaryCurrency').textContent = currencyText;

        const decimalSelect = document.getElementById('decimal_places');
        const decimalText = decimalSelect.options[decimalSelect.selectedIndex]?.text || '---';
        document.getElementById('summaryDecimal').textContent = decimalText;
    }

    // تحديث الملصف عند تغيير القيم
    document.getElementById('company_name').addEventListener('input', updateSummary);
    document.getElementById('fiscal_start_month').addEventListener('change', updateSummary);
    document.getElementById('default_currency_id').addEventListener('change', updateSummary);
    document.getElementById('decimal_places').addEventListener('change', updateSummary);


    // التحقق من صحة البيانات قبل الإرسال
    document.getElementById('setupForm').addEventListener('submit', function(e) {
        const companyName = document.getElementById('company_name').value.trim();
        const fiscalMonth = document.getElementById('fiscal_start_month').value;
        const currency = document.getElementById('default_currency_id').value;
        const decimalPlaces = document.getElementById('decimal_places').value;

        if (!companyName || !fiscalMonth || !currency || !decimalPlaces) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول الإلزامية');
            return;
        }

        // إظهار نموذج التحميل
        document.getElementById('loadingModal').classList.remove('hidden');
    });

    // تهيئة الملخص عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        updateSummary();

        // التحقق من حالة النظام
        checkSystemStatus();
    });

    // التحقق من حالة النظام
    async function checkSystemStatus() {
        try {
            const response = await fetch('{{ route("system-settings.check_status") }}');
            const status = await response.json();

            if (status.system_configured) {
                window.location.href = '{{ route("system-settings.index") }}';
            }
        } catch (error) {
            console.error('Error checking system status:', error);
        }
    }
</script>

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
@endsection