@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">إدارة الصلاحيات</h1>
        <a href="{{ route('permissions.create') }}" class="px-4 py-2 bg-secondary text-primary rounded-lg font-semibold shadow-md hover:bg-secondary/80 transition">
            <i class="fas fa-plus ml-2"></i> إضافة صلاحية جديدة
        </a>
    </header>

    <section class="bg-white p-6 rounded-xl shadow-lg table-container">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary/10">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">اسم الصلاحية</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الوصف</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">عدد الأدوار</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-primary uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @foreach($permissions as $permission)
                <tr class="hover:bg-gray-50 transition {{ !$permission->isActive ? 'opacity-70' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $permission->PermissionName }}</td>
                    <td class="px-6 py-4">{{ $permission->Description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-purple-100 text-purple-800 text-xs px-3 py-1 rounded-full font-medium">
                            {{ $permission->roles_count }} دور
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="{{ $permission->isActive ? 'text-green-600' : 'text-red-600' }} font-medium">
                            <i class="fas fa-circle text-xs ml-1"></i>
                            {{ $permission->isActive ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center table-actions">
                        <a href="{{ route('permissions.edit', $permission->id) }}" class="text-primary hover:text-secondary mr-3">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</main>
@endsection