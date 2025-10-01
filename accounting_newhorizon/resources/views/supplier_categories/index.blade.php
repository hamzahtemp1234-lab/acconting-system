@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-tags ml-3 text-secondary"></i> تصنيفات الموردين
        </h1>
        <a href="{{ route('supplier-categories.create') }}"
            class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-2"></i> إضافة تصنيف
        </a>
    </header>

    @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الرمز</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحساب</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50 {{ $cat->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 font-mono">{{ $cat->code }}</td>
                        <td class="px-6 py-4">
                            {{ $cat->name }}
                            @if($cat->trashed())
                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">محذوف</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($cat->account)
                            {{ $cat->account->code }} - {{ $cat->account->name }}
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($cat->is_active ?? $cat->isActive ?? false)
                            <span class="px-2 text-xs rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-check ml-1"></i> نشط
                            </span>
                            @else
                            <span class="px-2 text-xs rounded-full bg-red-100 text-red-700">
                                <i class="fas fa-ban ml-1"></i> غير نشط
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(!$cat->trashed())
                            <a href="{{ route('supplier-categories.edit', $cat) }}" class="text-blue-600 ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('supplier-categories.destroy', $cat) }}" method="POST" class="inline"
                                onsubmit="return confirm('تأكيد حذف التصنيف؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">لا توجد سجلات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection