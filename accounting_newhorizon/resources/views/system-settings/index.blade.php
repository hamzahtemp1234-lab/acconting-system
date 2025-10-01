@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-cogs ml-3 text-secondary"></i> إعدادات النظام
        </h1>
        @if($setting)
        <a href="{{ route('system-settings.edit', $setting->id) }}" class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-edit ml-2"></i> تعديل الإعدادات
        </a>
        @endif
    </header>

    @if($setting)
    <!-- بطاقة إعدادات النظام -->
    <div class="max-w-6xl mx-auto">
        <!-- معلومات الشركة -->
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8 mb-6">
            <h3 class="text-xl font-semibold mb-6 text-primary border-b pb-3">
                <i class="fas fa-building ml-2 text-secondary"></i> معلومات الشركة
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- الشعار والمعلومات الأساسية -->
                <div class="flex flex-col items-center md:items-start space-y-6">
                    @if($setting->logo)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $setting->logo) }}" alt="شعار الشركة"
                            class="w-32 h-32 rounded-lg border-4 border-secondary shadow-lg object-cover">
                        <p class="text-sm text-gray-600 mt-2">شعار الشركة</p>
                    </div>
                    @else
                    <div class="w-32 h-32 rounded-lg bg-gradient-to-br from-secondary to-primary flex items-center justify-center border-4 border-white shadow-lg">
                        <i class="fas fa-building text-white text-3xl"></i>
                    </div>
                    @endif

                    <div class="text-center md:text-right">
                        <h2 class="text-2xl font-bold text-primary">{{ $setting->company_name }}</h2>
                        <p class="text-gray-600 mt-2">إعدادات النظام الأساسية</p>
                    </div>
                </div>

                <!-- معلومات الاتصال -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center ml-3">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                                <p class="font-medium text-gray-900">{{ $setting->email ?? 'غير محدد' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center ml-3">
                                <i class="fas fa-phone text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">رقم الهاتف</p>
                                <p class="font-medium text-gray-900">{{ $setting->phone ?? 'غير محدد' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center ml-3 mt-1">
                                <i class="fas fa-map-marker-alt text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">العنوان</p>
                                <p class="font-medium text-gray-900">{{ $setting->address ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإعدادات المالية والنظام -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- الإعدادات المالية -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-primary mb-4 border-b pb-2">
                    <i class="fas fa-chart-line ml-2 text-secondary"></i> الإعدادات المالية
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">بداية السنة المالية</span>
                        <span class="font-medium text-primary">
                            الشهر {{ $setting->fiscal_start_month }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">العملة الافتراضية</span>
                        <span class="font-medium text-primary">
                            @if($setting->currency)
                            {{ $setting->currency->name }} ({{ $setting->currency->symbol }})
                            @else
                            غير محدد
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">المنازل العشرية</span>
                        <span class="font-medium text-primary">
                            {{ $setting->decimal_places }} منزلة
                        </span>
                    </div>
                </div>
            </div>

            <!-- معلومات النظام -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-primary mb-4 border-b pb-2">
                    <i class="fas fa-info-circle ml-2 text-secondary"></i> معلومات النظام
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">تاريخ الإنشاء</span>
                        <span class="font-medium text-primary">{{ $setting->created_at->format('Y-m-d') }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">آخر تحديث</span>
                        <span class="font-medium text-primary">{{ $setting->updated_at->format('Y-m-d') }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">حالة الإعدادات</span>
                        <span class="font-medium text-green-600">نشط</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإعدادات الإضافية -->
        @if($setting->extra)
        <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-primary mb-4 border-b pb-2">
                <i class="fas fa-sliders-h ml-2 text-secondary"></i> الإعدادات الإضافية
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($setting->extra as $key => $value)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">{{ $key }}</span>
                    <span class="font-medium text-primary">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @else
    <!-- حالة عدم وجود إعدادات -->
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-cogs text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">لا توجد إعدادات للنظام</h3>
        <p class="text-gray-600 mb-6">يجب إعداد النظام أولاً قبل استخدامه</p>
        @if(!$setting)
        <a href="{{ route('system_setup.create') }}" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إعداد النظام
        </a>
        @endif
    </div>
    @endif
</main>
@endsection