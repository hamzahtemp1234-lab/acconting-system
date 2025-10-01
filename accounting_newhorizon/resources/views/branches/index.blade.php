@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-code-branch ml-3 text-secondary"></i> إدارة الفروع
        </h1>
        <div class="flex items-center space-x-2 space-x-reverse">
            <form method="GET" action="{{ route('branches.index') }}" class="hidden md:block">
                <div class="flex items-center bg-white rounded-lg border px-3 py-2">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="بحث بالاسم/الرمز/الهاتف"
                        class="outline-none text-sm w-64 mr-2">
                    <button class="text-gray-600"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <a href="{{ route('branches.create') }}"
                class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-plus ml-2"></i> إضافة فرع
            </a>
        </div>
    </header>

    {{-- بطاقات إحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg"><i class="fas fa-list text-blue-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">إجمالي الفروع</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg"><i class="fas fa-check-circle text-green-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['active'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">نشطة</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border-l-4 border-gray-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-gray-100 rounded-lg"><i class="fas fa-ban text-gray-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['inactive'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">غير نشطة</p>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الرمز</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($branches as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono">{{ $b->code }}</td>
                        <td class="px-6 py-4">{{ $b->name }}</td>
                        <td class="px-6 py-4">{{ $b->phone ?: '—' }}</td>
                        <td class="px-6 py-4 truncate max-w-xs" title="{{ $b->address }}">{{ $b->address ?: '—' }}</td>
                        <td class="px-6 py-4">
                            @if($b->is_active)
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
                            <a href="{{ route('branches.edit', $b) }}" class="text-blue-600 ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('branches.destroy', $b) }}" method="POST" class="inline"
                                onsubmit="return confirm('تأكيد حذف الفرع؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">لا توجد سجلات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $branches->withQueryString()->links() }}
        </div>
    </div>
</main>
@endsection