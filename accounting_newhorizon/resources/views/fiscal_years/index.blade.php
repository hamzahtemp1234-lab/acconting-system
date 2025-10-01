@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-calendar-alt ml-3 text-secondary"></i> إدارة السنوات المالية
        </h1>
        <a href="{{ route('fiscal-years.create') }}"
            class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة سنة مالية
        </a>
    </header>

    <!-- 🔹 بطاقات الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- إجمالي السنوات -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $fiscalYears->count() }}</p>
                    <p class="text-sm text-gray-600">إجمالي السنوات</p>
                </div>
            </div>
        </div>

        <!-- السنوات المفتوحة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-door-open text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $fiscalYears->where('is_closed', 0)->count() }}</p>
                    <p class="text-sm text-gray-600">السنوات المفتوحة</p>
                </div>
            </div>
        </div>

        <!-- السنوات المغلقة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fas fa-lock text-red-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $fiscalYears->where('is_closed', 1)->count() }}</p>
                    <p class="text-sm text-gray-600">السنوات المغلقة</p>
                </div>
            </div>
        </div>

        <!-- أحدث سنة مالية -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    @php
                    $latest = $fiscalYears->sortByDesc('start_date')->first();
                    @endphp
                    <p class="text-lg font-bold text-gray-800">
                        {{ $latest ? $latest->name : '---' }}
                    </p>
                    <p class="text-sm text-gray-600">أحدث سنة مالية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول السنوات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right">الاسم</th>
                        <th class="px-6 py-3 text-right">تاريخ البداية</th>
                        <th class="px-6 py-3 text-right">تاريخ النهاية</th>
                        <th class="px-6 py-3 text-right">الحالة</th>
                        <th class="px-6 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fiscalYears as $year)
                    <tr class="hover:bg-gray-50 {{ $year->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">{{ $year->name }}</td>
                        <td class="px-6 py-4">{{ $year->start_date }}</td>
                        <td class="px-6 py-4">{{ $year->end_date }}</td>
                        <td class="px-6 py-4">
                            @if($year->is_closed)
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">مغلقة</span>
                            @else
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">مفتوحة</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('fiscal-years.edit', $year->id) }}" class="text-blue-600">تعديل</a>
                            <form action="{{ route('fiscal-years.destroy', $year->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">لا توجد سنوات مالية</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection