@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-user ml-3 text-secondary"></i> الملف الشخصي
        </h1>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-edit ml-2"></i> تعديل الملف
            </a>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
                <i class="fas fa-home ml-2"></i> الرئيسية
            </a>
        </div>
    </header>

    <div class="max-w-4xl mx-auto">
        <!-- بطاقة المعلومات الشخصية -->
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8 mb-6">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6 space-x-reverse">
                <!-- صورة البروفايل -->
                <div class="text-center relative">
                    @if($user->avatar)
                    <!-- تصحيح مسار الصورة -->
                    <img src="{{ $user->avatar_url ?? asset('storage/' . $user->avatar) }}" alt="الصورة الحالية"
                        class="w-32 h-32 rounded-full border-4 border-secondary shadow-lg object-cover" />
                    <!-- زر حذف الصورة -->
                    <form action="{{ route('profile.remove-avatar') }}" method="POST" class="absolute -top-2 -right-2">
                        @csrf
                        @method('POST')
                        <button type="submit"
                            onclick="return confirm('هل أنت متأكد من حذف الصورة الشخصية؟')"
                            class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition shadow-lg">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </form>
                    @else
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-secondary to-primary flex items-center justify-center border-4 border-white shadow-lg mx-auto">
                        <span class="text-white text-4xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    @endif
                    <div class="mt-4">
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            <i class="fas fa-circle ml-1 text-xs"></i> {{ $user->IsActive ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>

                <!-- المعلومات -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">الاسم الكامل</label>
                        <p class="text-lg font-semibold text-primary">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">اسم المستخدم</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $user->Username ?? 'غير محدد' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
                        <p class="text-lg font-semibold text-gray-800">
                            @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="hover:text-secondary transition">
                                <i class="fas fa-phone ml-1"></i> {{ $user->phone }}
                            </a>
                            @else
                            غير محدد
                            @endif
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">العنوان</label>
                        <p class="text-lg font-semibold text-gray-800">
                            @if($user->address)
                            <i class="fas fa-map-marker-alt ml-1 text-red-500"></i> {{ $user->address }}
                            @else
                            غير محدد
                            @endif
                        </p>
                    </div>

                    <!-- معلومات إضافية -->
                    <div class="md:col-span-2 border-t pt-4 mt-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="text-blue-600 font-bold text-lg">{{ $user->roles->count() }}</div>
                                <div class="text-blue-500 text-sm">عدد الأدوار</div>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-green-600 font-bold text-lg">{{ $user->created_at->diffInDays(now()) }}</div>
                                <div class="text-green-500 text-sm">أيام في النظام</div>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <div class="text-purple-600 font-bold text-lg">{{ $user->email_verified_at ? 'مفعل' : 'غير مفعل' }}</div>
                                <div class="text-purple-500 text-sm">البريد الإلكتروني</div>
                            </div>
                            <div class="bg-orange-50 p-3 rounded-lg">
                                <div class="text-orange-600 font-bold text-lg">{{ $user->logs_count ?? 0 }}</div>
                                <div class="text-orange-500 text-sm">عدد السجلات</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة الأدوار والصلاحيات -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- الأدوار -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-xl font-semibold text-primary mb-4 border-b pb-2">
                    <i class="fas fa-user-tag ml-2 text-secondary"></i> الأدوار
                </h3>
                <div class="space-y-2">
                    @forelse($user->roles as $role)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div>
                            <span class="font-medium text-gray-800">{{ $role->RoleName }}</span>
                            @if($role->pivot->isActive)
                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full mr-2">نشط</span>
                            @else
                            <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full mr-2">غير نشط</span>
                            @endif
                        </div>
                        <span class="text-sm text-gray-500">{{ $role->pivot->created_at->format('Y-m-d') }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-info-circle ml-1"></i> لا توجد أدوار مخصصة
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-xl font-semibold text-primary mb-4 border-b pb-2">
                    <i class="fas fa-chart-bar ml-2 text-secondary"></i> الإحصائيات
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded transition">
                        <span class="text-gray-600">
                            <i class="fas fa-calendar-plus ml-1 text-blue-500"></i> تاريخ الانضمام:
                        </span>
                        <span class="font-medium">{{ $user->created_at->translatedFormat('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded transition">
                        <span class="text-gray-600">
                            <i class="fas fa-edit ml-1 text-green-500"></i> آخر تحديث:
                        </span>
                        <span class="font-medium">{{ $user->updated_at->translatedFormat('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded transition">
                        <span class="text-gray-600">
                            <i class="fas fa-shield-alt ml-1 text-purple-500"></i> عدد الأدوار:
                        </span>
                        <span class="font-medium text-primary">{{ $user->roles->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded transition">
                        <span class="text-gray-600">
                            <i class="fas fa-user-check ml-1 text-orange-500"></i> حالة الحساب:
                        </span>
                        <span class="font-medium {{ $user->IsActive ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->IsActive ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded transition">
                        <span class="text-gray-600">
                            <i class="fas fa-envelope ml-1 text-indigo-500"></i> البريد الإلكتروني:
                        </span>
                        <span class="font-medium {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->email_verified_at ? 'مفعل' : 'غير مفعل' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="mt-6 flex flex-wrap justify-center gap-4">
            <a href="{{ route('profile.edit') }}" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg">
                <i class="fas fa-edit ml-2"></i> تعديل الملف
            </a>
            <a href="{{ route('profile.change-password') }}" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium shadow-lg">
                <i class="fas fa-key ml-2"></i> تغيير كلمة المرور
            </a>
            <a href="{{ route('profile.activity-log') }}" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium shadow-lg">
                <i class="fas fa-history ml-2"></i> سجل النشاطات
            </a>
            @if($user->avatar)
            <form action="{{ route('profile.remove-avatar') }}" method="POST" class="inline">
                @csrf
                @method('POST')
                <button type="submit"
                    onclick="return confirm('هل أنت متأكد من حذف الصورة الشخصية؟')"
                    class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium shadow-lg">
                    <i class="fas fa-trash ml-2"></i> حذف الصورة
                </button>
            </form>
            @endif
        </div>

        <!-- بطاقة النشاط الأخير -->
        <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-primary mb-4 border-b pb-2">
                <i class="fas fa-clock ml-2 text-secondary"></i> آخر النشاطات
            </h3>
            <div class="space-y-3">
                @php
                $recentActivities = \App\Models\AuditTrail::where('ChangedBy', $user->id)
                ->orderBy('ChangeDate', 'desc')
                ->limit(5)
                ->get();
                @endphp

                @forelse($recentActivities as $activity)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center 
                            @if($activity->ChangeType == 'I') bg-green-100 text-green-600
                            @elseif($activity->ChangeType == 'U') bg-blue-100 text-blue-600
                            @elseif($activity->ChangeType == 'D') bg-red-100 text-red-600
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($activity->ChangeType == 'I') <i class="fas fa-plus text-xs"></i>
                            @elseif($activity->ChangeType == 'U') <i class="fas fa-edit text-xs"></i>
                            @elseif($activity->ChangeType == 'D') <i class="fas fa-trash text-xs"></i>
                            @else <i class="fas fa-info text-xs"></i> @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $activity->ChangeDescription }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->TableName }}</p>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $activity->ChangeDate->diffForHumans() }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-info-circle ml-1"></i> لا توجد نشاطات حديثة
                </div>
                @endforelse

                @if($recentActivities->count() > 0)
                <div class="text-center pt-4">
                    <a href="{{ route('profile.activity-log') }}" class="text-secondary hover:text-primary transition font-medium">
                        <i class="fas fa-list ml-1"></i> عرض جميع النشاطات
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
@endsection