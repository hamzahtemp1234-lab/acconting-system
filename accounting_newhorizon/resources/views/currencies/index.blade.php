@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-coins ml-3 text-secondary"></i> إدارة العملات
        </h1>
        <a href="{{ route('currencies.create') }}" class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة عملة
        </a>
    </header>

    <!-- بطاقة الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $currencies->count() }}</p>
                    <p class="text-sm text-gray-600">إجمالي العملات</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $currencies->where('deleted_at', null)->count() }}</p>
                    <p class="text-sm text-gray-600">العملات النشطة</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-globe text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $currencies->unique('code')->count() }}</p>
                    <p class="text-sm text-gray-600">رموز فريدة</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $defaultCurrency ?? 0 }}</p>
                    <p class="text-sm text-gray-600">عملة افتراضية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول العملات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رمز العملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرمز</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر سعر صرف</th> <!-- ✅ عمود جديد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($currencies as $currency)
                    <tr class="hover:bg-gray-50 transition duration-150 {{ $currency->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center ml-3">
                                    <span class="text-white font-bold text-sm">{{ $currency->code }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $currency->code }}</div>
                                    <div class="text-xs text-gray-500">رمز ISO</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $currency->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-gray-900">{{ $currency->symbol ?? '---' }}</div>
                        </td>
                        <!-- ✅ عرض آخر سعر صرف -->
                        <td class="px-6 py-4">
                            @if($currency->exchangeRates->first())
                            <span class="text-green-600 font-bold">
                                {{ $currency->exchangeRates->first()->rate }}
                            </span>
                            <span class="text-gray-500 text-xs">
                                ({{ $currency->exchangeRates->first()->from_date_exchange }})
                            </span>
                            @else
                            <span class="text-gray-400">لا يوجد</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($currency->trashed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-ban ml-1"></i> محذوفة
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check ml-1"></i> نشطة
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <!-- ✅ زر إضافة سعر صرف -->
                            <a href="{{ route('exchange-rates.create', $currency->id) }}" class="ml-3 text-purple-600">
                                <i class="fas fa-dollar-sign"></i> إضافة سعر
                            </a>
                            @if(!$currency->trashed())
                            <a href="{{ route('currencies.edit', $currency->id) }}" class="text-secondary hover:text-primary ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه العملة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 mr-3">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400">لا توجد إجراءات</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-coins text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">لا توجد عملات</p>
                                <p class="text-gray-400 text-sm mt-2">ابدأ بإضافة أول عملة إلى النظام</p>
                                <a href="{{ route('currencies.create') }}" class="mt-4 px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                                    <i class="fas fa-plus ml-2"></i> إضافة عملة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- رابط العودة -->
    <div class="mt-6 text-center">
        <a href="{{ route('system-settings.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة لإعدادات النظام
        </a>
    </div>
</main>
@endsection