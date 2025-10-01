@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل إعدادات النظام
        </h1>
        <a href="{{ route('system-settings.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للإعدادات
        </a>
    </header>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
            <form action="{{ route('system-settings.update', $setting->id) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- معلومات الشركة -->
                <div class="border-b border-gray-200 pb-8 mb-8">
                    <h3 class="text-xl font-semibold mb-6 text-primary border-r-4 border-secondary pr-3">
                        <i class="fas fa-building ml-2 text-secondary"></i> معلومات الشركة
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- اسم الشركة -->
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم الشركة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="company_name" name="company_name"
                                value="{{ old('company_name', $setting->company_name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                                placeholder="أدخل اسم الشركة" required>
                            @error('company_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الشعار -->
                        <div class="md:col-span-2">
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                شعار الشركة
                            </label>

                            <div class="flex flex-col space-y-4">
                                @if($setting->logo)
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <img src="{{ asset('storage/' . $setting->logo) }}"
                                        alt="الشعار الحالي"
                                        class="w-24 h-24 object-cover rounded-lg border">
                                    <span class="text-sm text-gray-600">الشعار الحالي</span>
                                </div>
                                @endif

                                <input type="file" id="logo" name="logo"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                                    accept="image/*">
                                <p class="text-xs text-gray-500">JPG, PNG, GIF — الحد الأقصى 2MB</p>
                                @error('logo')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                البريد الإلكتروني
                            </label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $setting->email) }}"
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
                                value="{{ old('phone', $setting->phone) }}"
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
                                placeholder="أدخل العنوان الكامل">{{ old('address', $setting->address) }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- الإعدادات المالية -->
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
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('fiscal_start_month', $setting->fiscal_start_month) == $i ? 'selected' : '' }}>
                                    الشهر {{ $i }} - {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                    @endfor
                            </select>
                            @error('fiscal_start_month')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- العملة الافتراضية -->
                        <div>
                            <label for="default_currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                                العملة الافتراضية
                            </label>
                            <select id="default_currency_id" name="default_currency_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                                <option value="">اختر العملة</option>
                                @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('default_currency_id', $setting->default_currency_id) == $currency->id ? 'selected' : '' }}>
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
                                @for($i = 0; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('decimal_places', $setting->decimal_places) == $i ? 'selected' : '' }}>
                                    {{ $i }} منزلة
                                    </option>
                                    @endfor
                            </select>
                            @error('decimal_places')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 space-x-reverse">
                    <a href="{{ route('system-settings.index') }}"
                        class="px-8 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-center">
                        <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30 flex items-center justify-center">
                        <i class="fas fa-save ml-2"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>

        <!-- رابط إدارة العملات -->
        <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200 p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">إدارة العملات</h3>
            <p class="text-gray-600 mb-4">يمكنك إضافة وتعديل العملات المتاحة في النظام</p>
            <a href="{{ route('currencies.index') }}"
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
                <i class="fas fa-coins ml-2"></i> الذهاب لإدارة العملات
            </a>
        </div>
    </div>
</main>

<script>
    // التحقق من صحة البيانات قبل الإرسال
    document.querySelector('form').addEventListener('submit', function(e) {
        const companyName = document.getElementById('company_name').value.trim();
        if (!companyName) {
            e.preventDefault();
            alert('يرجى إدخال اسم الشركة');
            return;
        }
    });
</script>
@endsection