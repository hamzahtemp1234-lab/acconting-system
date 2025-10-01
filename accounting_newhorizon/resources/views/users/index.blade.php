@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">إدارة المستخدمين</h1>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-secondary text-primary rounded-lg font-semibold shadow-md hover:bg-secondary/80 transition">
            <i class="fas fa-plus ml-2"></i> إضافة مستخدم جديد
        </a>
    </header>

    <section class="bg-white p-6 rounded-xl shadow-lg mb-8 search-section">
        <div class="search-input-container">
            <input type="text" placeholder="البحث باسم المستخدم أو البريد الإلكتروني" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
            <i class="fas fa-search"></i>
        </div>

        <div class="filter-container">
            <select class="py-2 px-4 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
                <option>تصفية حسب الدور</option>
                <option>مدير النظام</option>
                <option>محاسب رئيسي</option>
                <option>مدخل بيانات</option>
            </select>
        </div>

        <div class="text-primary font-bold text-lg user-count">
            إجمالي المستخدمين: <span class="text-secondary">{{ $users->total() }}</span>
        </div>
    </section>

    <section class="bg-white p-6 rounded-xl shadow-lg table-container">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary/10">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">معلومات الاتصال</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الدور</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-primary uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition {{ !$user->IsActive ? 'opacity-70' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <!-- الصورة الشخصية -->
                            <div class="flex-shrink-0">
                                @if($user->avatar)
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200"
                                    src="{{ asset('storage/' . $user->avatar) }}"
                                    alt="{{ $user->name }}">
                                @else
                                <div class="h-10 w-10 rounded-full bg-secondary flex items-center justify-center text-primary font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                @endif
                            </div>
                            <!-- معلومات المستخدم -->
                            <div>
                                <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-gray-500 text-xs">{{ $user->Username }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-gray-900">{{ $user->email }}</div>
                        @if($user->phone)
                        <div class="text-gray-500 text-xs mt-1">
                            <i class="fas fa-phone ml-1"></i> {{ $user->phone }}
                        </div>
                        @endif
                        @if($user->address)
                        <div class="text-gray-500 text-xs mt-1 truncate max-w-xs">
                            <i class="fas fa-map-marker-alt ml-1"></i> {{ Str::limit($user->address, 30) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @foreach($user->roles as $role)
                        <span class="bg-{{ $role->RoleName == 'مدير النظام' ? 'red' : ($role->RoleName == 'محاسب رئيسي' ? 'secondary/20' : 'gray') }}-100 text-{{ $role->RoleName == 'مدير النظام' ? 'red' : ($role->RoleName == 'محاسب رئيسي' ? 'primary' : 'gray') }}-800 text-xs px-3 py-1 rounded-full font-medium">
                            {{ $role->RoleName }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="{{ $user->IsActive ? 'text-green-600' : 'text-red-600' }} font-medium">
                            <i class="fas fa-circle text-xs ml-1"></i>
                            {{ $user->IsActive ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center table-actions">
                        <a href="{{ route('users.edit', $user->id) }}" class="text-primary hover:text-secondary mr-3">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
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

        <!-- الترقيم -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </section>
</main>
@endsection