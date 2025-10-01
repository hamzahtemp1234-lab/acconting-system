@extends('layouts.app')

@section('title', 'لوحة التحكم - النظام المحاسبي')

@section('content')
<<header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
    <h1 class="text-3xl font-extrabold text-primary">لوحة التحكم المحاسبية</h1>
    <div class="space-x-4 space-x-reverse header-buttons">
        <button class="px-4 py-2 bg-secondary text-primary rounded-lg font-semibold shadow-md hover:bg-secondary/80 transition">
            + قيد يومية جديد
        </button>
        <button class="px-4 py-2 bg-primary text-white rounded-lg font-semibold shadow-md hover:bg-primary/80 transition">
            + سند قبض/صرف
        </button>
    </div>
    </header>

    <section class="stats-grid grid gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow-lg border-r-4 border-secondary transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500">الرصيد النقدي الفوري</p>
            <p class="text-3xl font-bold text-primary mt-1">250,500 <span class="text-secondary text-xl">ريال</span></p>
            <p class="text-xs text-green-500 mt-2">+5% عن الشهر الماضي</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border-r-4 border-primary/50 transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500">إجمالي الذمم المدينة</p>
            <p class="text-3xl font-bold text-primary mt-1">112,800 <span class="text-gray-400 text-xl">ريال</span></p>
            <p class="text-xs text-red-500 mt-2">3 فواتير متأخرة الدفع</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border-r-4 border-primary/50 transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500">إجمالي الذمم الدائنة</p>
            <p class="text-3xl font-bold text-primary mt-1">85,200 <span class="text-gray-400 text-xl">ريال</span></p>
            <p class="text-xs text-green-500 mt-2">المطلوب سداده خلال 7 أيام</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border-r-4 border-secondary transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500">قيود اليومية غير المعتمدة</p>
            <p class="text-3xl font-bold text-secondary mt-1">4</p>
            <p class="text-xs text-gray-500 mt-2">تحتاج مراجعة المدير المالي</p>
        </div>
    </section>

    <section class="main-grid grid gap-6">

        <div class="space-y-6">

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-primary">صافي التدفق النقدي (آخر 6 أشهر)</h3>
                <canvas id="cashFlowChart" class="h-80"></canvas>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-primary">آخر القيود المسجلة</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدين</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدائن</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المصدر</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">2025-09-27</td>
                                <td class="px-6 py-4 whitespace-nowrap">تحصيل دفعة من العميل (أحمد)</td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600">0.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-600">12,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="bg-secondary/20 text-secondary text-xs px-2 py-1 rounded-full">سند قبض</span></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">2025-09-26</td>
                                <td class="px-6 py-4 whitespace-nowrap">سداد فاتورة المورد (الشرق)</td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600">8,900.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-600">0.00</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="bg-primary/20 text-primary text-xs px-2 py-1 rounded-full">سند صرف</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-primary">
                <h3 class="text-xl font-semibold mb-4 text-primary">حالة التقارير</h3>
                <ul class="space-y-3">
                    <li class="flex justify-between items-center text-sm">
                        <span>ميزانية المراجعة (حتى اليوم)</span>
                        <a href="#" class="text-secondary hover:underline font-medium">عرض</a>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span>الأستاذ العام</span>
                        <a href="#" class="text-secondary hover:underline font-medium">عرض</a>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span>العملاء المدينة (أقدم 30 يوماً)</span>
                        <a href="#" class="text-secondary hover:underline font-medium">تقرير</a>
                    </li>
                </ul>
            </div>

            <div class="bg-red-50 p-6 rounded-xl shadow-lg border-t-4 border-red-500">
                <h3 class="text-xl font-semibold mb-4 text-red-700">تنبيهات هامة</h3>
                <p class="text-sm text-red-600 mb-2">**تنبيه:** حساب الصندوق **(1011)** تجاوز الحد الأقصى المسموح به.</p>
                <p class="text-sm text-red-600">**مراجعة:** القيود **#201** غير متوازنة (بفارق 500 ريال).</p>
            </div>
        </div>
    </section>
    @endsection