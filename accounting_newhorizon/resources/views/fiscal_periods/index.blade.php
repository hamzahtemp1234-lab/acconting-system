@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <!-- رأس الصفحة -->
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-calendar-week ml-3 text-secondary"></i> إدارة الفترات المالية
        </h1>
        <a href="{{ route('fiscal-periods.create') }}"
            class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة فترة مالية
        </a>
    </header>

    <!-- 🔹 بطاقات الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- إجمالي الفترات -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6 flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-calendar-week text-blue-600 text-xl"></i>
            </div>
            <div class="mr-3">
                <p class="text-2xl font-bold text-gray-800">{{ $periods->count() }}</p>
                <p class="text-sm text-gray-600">إجمالي الفترات</p>
            </div>
        </div>

        <!-- الفترات المفتوحة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6 flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="fas fa-door-open text-green-600 text-xl"></i>
            </div>
            <div class="mr-3">
                <p class="text-2xl font-bold text-gray-800">{{ $periods->where('is_closed', 0)->count() }}</p>
                <p class="text-sm text-gray-600">الفترات المفتوحة</p>
            </div>
        </div>

        <!-- الفترات المغلقة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-6 flex items-center">
            <div class="p-3 bg-red-100 rounded-lg">
                <i class="fas fa-lock text-red-600 text-xl"></i>
            </div>
            <div class="mr-3">
                <p class="text-2xl font-bold text-gray-800">{{ $periods->where('is_closed', 1)->count() }}</p>
                <p class="text-sm text-gray-600">الفترات المغلقة</p>
            </div>
        </div>

        <!-- أحدث فترة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-purple-500 p-6 flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
                <i class="fas fa-star text-purple-600 text-xl"></i>
            </div>
            <div class="mr-3">
                @php
                $latest = $periods->sortByDesc('start_date')->first();
                @endphp
                <p class="text-lg font-bold text-gray-800">
                    {{ $latest ? 'فترة #' . $latest->period_no . ' - ' . $latest->fiscalYear->name : '---' }}
                </p>
                <p class="text-sm text-gray-600">أحدث فترة</p>
            </div>
        </div>
    </div>

    <!-- جدول الفترات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السنة المالية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الفترة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ البداية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ النهاية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periods as $period)
                    <tr class="hover:bg-gray-50 transition duration-150 {{ $period->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $period->fiscalYear->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                            #{{ $period->period_no }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $period->start_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $period->end_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($period->trashed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-600">
                                محذوفة
                            </span>
                            @elseif($period->is_closed)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                مغلقة
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                مفتوحة
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            @if($period->trashed())
                            <form action="{{ route('fiscal-periods.restore', $period->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-green-600 hover:text-green-800">استعادة</button>
                            </form>
                            <form action="{{ route('fiscal-periods.forceDelete', $period->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف النهائي؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 ml-3">حذف نهائي</button>
                            </form>
                            @else
                            <a href="{{ route('fiscal-periods.edit', $period->id) }}" class="text-blue-600 hover:text-blue-800 ml-3">تعديل</a>
                            <form action="{{ route('fiscal-periods.destroy', $period->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">حذف</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">لا توجد فترات مالية</p>
                                <a href="{{ route('fiscal-periods.create') }}" class="mt-4 px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                                    <i class="fas fa-plus ml-2"></i> إضافة فترة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection