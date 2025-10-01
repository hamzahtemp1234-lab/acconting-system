@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <!-- العنوان + زر إضافة -->
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-sitemap ml-3 text-secondary"></i> إدارة أنواع الحسابات
        </h1>
        <a href="{{ route('account-types.create') }}"
            class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة نوع جديد
        </a>
    </header>

    <!-- كروت إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- عدد الأنواع -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $types->count() }}</p>
                    <p class="text-sm text-gray-600">إجمالي الأنواع</p>
                </div>
            </div>
        </div>

        <!-- الأنواع النشطة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $types->where('is_active', 1)->count() }}</p>
                    <p class="text-sm text-gray-600">الأنواع النشطة</p>
                </div>
            </div>
        </div>

        <!-- الأنواع حسب الطبيعة -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-balance-scale text-purple-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-lg font-semibold text-gray-800">
                        مدين: {{ $types->where('nature','debit')->count() }} /
                        دائن: {{ $types->where('nature','credit')->count() }}
                    </p>
                    <p class="text-sm text-gray-600">التوزيع حسب الطبيعة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول أنواع الحسابات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطبيعة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($types as $type)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <!-- الاسم -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $type->name }}
                        </td>

                        <!-- الطبيعة -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($type->nature == 'debit')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                مدين
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                دائن
                            </span>
                            @endif
                        </td>

                        <!-- الحالة -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($type->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                نشط
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                غير نشط
                            </span>
                            @endif
                        </td>

                        <!-- الإجراءات -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('account-types.edit', $type->id) }}"
                                class="text-secondary hover:text-primary ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('account-types.destroy', $type->id) }}" method="POST"
                                class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 mr-3">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-info-circle text-gray-400 ml-2"></i>
                            لا توجد أنواع حسابات مضافة بعد
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</main>
@endsection