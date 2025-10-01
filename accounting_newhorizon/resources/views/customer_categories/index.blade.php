@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-layer-group ml-3 text-secondary"></i> تصنيفات العملاء
        </h1>
        <a href="{{ route('customer-categories.create') }}" class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة تصنيف
        </a>
    </header>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right">الكود</th>
                        <th class="px-6 py-3 text-right">الاسم</th>
                        <th class="px-6 py-3 text-right">الوصف</th>
                        <th class="px-6 py-3 text-right">الحالة</th>
                        <th class="px-6 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($categories as $category)
                    <tr class="{{ $category->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">{{ $category->code }}</td>
                        <td class="px-6 py-4">{{ $category->name }}</td>
                        <td class="px-6 py-4">{{ $category->description ?? '---' }}</td>
                        <td class="px-6 py-4">
                            @if($category->is_active == FALSE)
                            <span class="text-red-600">غير مفعل</span>
                            @else
                            <span class="text-green-600">نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('customer-categories.edit', $category->id) }}" class="text-blue-600 ml-3"><i class="fas fa-edit"></i> تعديل</a>
                            <form action="{{ route('customer-categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد الحذف؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600"><i class="fas fa-trash"></i> حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">لا يوجد تصنيفات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</main>
@endsection