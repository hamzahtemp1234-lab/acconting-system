@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-user-tie ml-3 text-secondary"></i> إدارة الوكلاء
        </h1>
        <div class="flex items-center space-x-2 space-x-reverse">
            <form method="GET" action="{{ route('agents.index') }}" class="hidden md:block">
                <div class="flex items-center bg-white rounded-lg border px-3 py-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="بحث بالاسم/الكود/الهاتف/الإيميل"
                        class="outline-none text-sm w-64 mr-2">
                    <button class="text-gray-600"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <a href="{{ route('agents.create') }}"
                class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-plus ml-2"></i> إضافة وكيل
            </a>
        </div>
    </header>

    <!-- بطاقات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg"><i class="fas fa-users text-blue-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-600">إجمالي الوكلاء</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg"><i class="fas fa-check-circle text-green-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['active'] }}</p>
                    <p class="text-sm text-gray-600">نشط</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg"><i class="fas fa-building text-purple-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['company'] }}</p>
                    <p class="text-sm text-gray-600">شركات</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg"><i class="fas fa-user text-yellow-600"></i></div>
                <div class="mr-3">
                    <p class="text-2xl font-bold">{{ $stats['person'] }}</p>
                    <p class="text-sm text-gray-600">أفراد</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الكود</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">العمولة %</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحساب</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($agents as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono">{{ $a->code }}</td>
                        <td class="px-6 py-4">{{ $a->name }}</td>
                        <td class="px-6 py-4">{{ $a->type === 'company' ? 'شركة' : 'فرد' }}</td>
                        <td class="px-6 py-4">{{ number_format($a->commission_rate, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($a->account)
                            {{ $a->account->code }} - {{ $a->account->name }}
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($a->is_active)
                            <span class="px-2 text-xs rounded-full bg-green-100 text-green-700"><i class="fas fa-check ml-1"></i> نشط</span>
                            @else
                            <span class="px-2 text-xs rounded-full bg-red-100 text-red-700"><i class="fas fa-ban ml-1"></i> غير نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('agents.edit',$a->id) }}" class="text-blue-600 ml-3"><i class="fas fa-edit"></i> تعديل</a>
                            <form action="{{ route('agents.destroy',$a->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد حذف الوكيل؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-600"><i class="fas fa-trash"></i> حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">لا توجد سجلات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $agents->withQueryString()->links() }}
        </div>
    </div>
</main>
@endsection