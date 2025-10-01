@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-6 pb-4 border-b">
        <h1 class="text-2xl font-extrabold text-primary">
            <i class="fas fa-project-diagram ml-2 text-secondary"></i> مراكز التكلفة
        </h1>
        <a href="{{ route('cost-centers.create') }}"
            class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
            <i class="fas fa-plus ml-1"></i> إضافة مركز
        </a>
    </header>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكود</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستوى</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مجموعة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($centers as $center)
                <tr class="{{ $center->trashed() ? 'opacity-50 bg-red-50' : '' }}">
                    <td class="px-6 py-4 font-bold">{{ $center->code }}</td>
                    <td class="px-6 py-4">{{ $center->name }}</td>
                    <td class="px-6 py-4">{{ $center->type->name ?? '---' }}</td>
                    <td class="px-6 py-4">{{ $center->level }}</td>
                    <td class="px-6 py-4">{{ $center->is_group ? '✅' : '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $center->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $center->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center space-x-2 space-x-reverse">
                        @if(!$center->trashed())
                        <a href="{{ route('cost-centers.edit', $center->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i> </a>
                        <form action="{{ route('cost-centers.destroy', $center->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </form>
                        @else
                        <form action="{{ route('cost-centers.restore', $center->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800">♻️ استرجاع</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">لا توجد مراكز تكلفة بعد.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
@endsection