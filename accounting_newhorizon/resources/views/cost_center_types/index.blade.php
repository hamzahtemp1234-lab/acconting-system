@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-sitemap ml-3 text-secondary"></i> أنواع مراكز التكلفة
        </h1>
        <a href="{{ route('cost-center-types.create') }}"
            class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة نوع
        </a>
    </header>

    <!-- بطاقات الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6 flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-sitemap text-blue-600 text-xl"></i>
            </div>
            <div class="mr-3">
                <p class="text-2xl font-bold text-gray-800">{{ $types->count() }}</p>
                <p class="text-sm text-gray-600">إجمالي الأنواع</p>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرمز</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($types as $type)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $type->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $type->name }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('cost-center-types.edit', $type->id) }}" class="text-blue-600 hover:text-blue-800 ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('cost-center-types.destroy', $type->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            لا توجد أنواع مراكز تكلفة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection