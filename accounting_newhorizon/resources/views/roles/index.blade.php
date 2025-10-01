@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">إدارة الأدوار</h1>
        <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-secondary text-primary rounded-lg font-semibold shadow-md hover:bg-secondary/80 transition">
            <i class="fas fa-plus ml-2"></i> إضافة دور جديد
        </a>
    </header>

    <section class="bg-white p-6 rounded-xl shadow-lg mb-8">
        <div class="search-input-container">
            <input type="text" placeholder="البحث باسم الدور" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
            <i class="fas fa-search"></i>
        </div>
    </section>

    <section class="bg-white p-6 rounded-xl shadow-lg table-container">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary/10">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">اسم الدور</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الوصف</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">عدد المستخدمين</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-primary uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50 transition {{ !$role->isActive ? 'opacity-70' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $role->RoleName }}</td>
                    <td class="px-6 py-4">{{ $role->Description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                            {{ $role->users_count }} مستخدم
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="{{ $role->isActive ? 'text-green-600' : 'text-red-600' }} font-medium">
                            <i class="fas fa-circle text-xs ml-1"></i>
                            {{ $role->isActive ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center table-actions">
                        <a href="{{ route('roles.edit', $role->id) }}" class="text-primary hover:text-secondary mr-3">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('roles.permissions', $role->id) }}" class="text-green-600 hover:text-green-800 mr-3">
                            <i class="fas fa-shield-alt"></i> الصلاحيات
                        </a>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline">
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