@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <!-- رأس الصفحة -->
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-users ml-3 text-secondary"></i> إدارة العملاء
        </h1>

        <div class="flex items-center space-x-2 space-x-reverse">
            <form method="GET" action="{{ route('customers.index') }}" class="hidden md:block">
                <div class="flex items-center bg-white rounded-lg border px-3 py-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="بحث بالاسم/الكود/الهاتف"
                        class="outline-none text-sm w-64">
                    <button class="ml-2 text-gray-600"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <a href="{{ route('customers.create') }}"
                class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-plus ml-2"></i> إضافة عميل
            </a>
        </div>
    </header>

    <!-- بطاقات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-600">إجمالي العملاء</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['active'] }}</p>
                    <p class="text-sm text-gray-600">عملاء نشطون</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-building text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['companies'] }}</p>
                    <p class="text-sm text-gray-600">شركات</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-user text-purple-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['individuals'] }}</p>
                    <p class="text-sm text-gray-600">أفراد</p>
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $c)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 font-semibold">{{ $c->code }}</td>
                        <td class="px-6 py-4">{{ $c->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs bg-gray-100">
                                {{ $c->type === 'company' ? 'شركة' : 'فرد' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $c->phone ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $c->email ?? '—' }}</td>
                        <td class="px-6 py-4">{{ optional($c->currency)->code ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($c->is_active)
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
                            <a href="{{ route('customers.edit',$c->id) }}" class="text-blue-600 ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('customers.destroy',$c->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('تأكيد حذف العميل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">لا توجد سجلات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50">
            {{ $customers->withQueryString()->links() }}
        </div>
    </div>

</main>
@endsection