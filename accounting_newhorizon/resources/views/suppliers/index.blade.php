@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <!-- رأس الصفحة -->
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-truck ml-3 text-secondary"></i> إدارة الموردين
        </h1>

        <div class="flex items-center space-x-2 space-x-reverse">
            <form method="GET" action="{{ route('suppliers.index') }}" class="hidden md:block">
                <div class="flex items-center bg-white rounded-lg border px-3 py-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="بحث بالاسم/الكود/الهاتف/الإيميل"
                        class="outline-none text-sm w-64">
                    <button class="ml-2 text-gray-600"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <a href="{{ route('suppliers.create') }}"
                class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-plus ml-2"></i> إضافة مورد
            </a>
        </div>
    </header>

    <!-- بطاقات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-truck text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">إجمالي الموردين</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['active'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">نشط</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-link text-purple-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['with_account'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">لديهم حساب بالدليل</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكود</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التصنيف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $s)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 font-semibold">{{ $s->code }}</td>
                        <td class="px-6 py-4">{{ $s->name }}</td>
                        <td class="px-6 py-4">{{ $s->phone ?: '—' }}</td>
                        <td class="px-6 py-4">{{ $s->email ?: '—' }}</td>
                        <td class="px-6 py-4">{{ optional($s->category)->name ?: '—' }}</td>
                        <td class="px-6 py-4">
                            @if($s->is_active)
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-check ml-1"></i> نشط
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                <i class="fas fa-ban ml-1"></i> غير نشط
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('suppliers.edit', $s) }}" class="text-blue-600 ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="inline"
                                onsubmit="return confirm('تأكيد حذف المورد؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">لا توجد سجلات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    </div>

</main>
@endsection