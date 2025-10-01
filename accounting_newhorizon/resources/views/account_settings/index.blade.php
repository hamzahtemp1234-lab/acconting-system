@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-sliders-h ml-3 text-secondary"></i> إعدادات الحسابات
        </h1>

        <div class="flex items-center space-x-2 space-x-reverse">
            <form method="GET" action="{{ route('account-settings.index') }}" class="hidden md:block">
                <div class="flex items-center bg-white rounded-lg border px-3 py-2">
                    <select name="module" class="text-sm border-r pr-2 mr-2">
                        <option value="">كل الوحدات</option>
                        @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module')==$m?'selected':'' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="q" value="{{ $q }}" placeholder="بحث بالمفتاح/الملاحظات"
                        class="outline-none text-sm w-64 mr-2">
                    <button class="text-gray-600"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <a href="{{ route('account-settings.create') }}"
                class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-plus ml-2"></i> إضافة إعداد
            </a>
        </div>
    </header>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الوحدة</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">المفتاح</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحساب</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">النطاق</th>
                        <th class="px-6 py-3 text-right text-xs text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($settings as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold">{{ $s->module }}</td>
                        <td class="px-6 py-4">{{ $s->key }}</td>
                        <td class="px-6 py-4">
                            @if($s->account)
                            {{ $s->account->code }} - {{ $s->account->name }}
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ $s->scopeLabel() }}</span>
                            @if($s->scopeName())
                            <span class="text-gray-500 text-xs mr-2">({{ $s->scopeName() }})</span>
                            @endif
                        </td>
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
                            <a href="{{ route('account-settings.edit',$s->id) }}" class="text-blue-600 ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('account-settings.destroy',$s->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('تأكيد حذف الإعداد؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">لا توجد إعدادات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $settings->withQueryString()->links() }}
        </div>
    </div>

</main>
@endsection