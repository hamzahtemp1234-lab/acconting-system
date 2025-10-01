@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-eye ml-3 text-secondary"></i> تفاصيل سجل التدقيق
        </h1>
        <a href="{{ route('audit-trails.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-4xl mx-auto">
        <!-- بطاقة التفاصيل -->
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary overflow-hidden">
            <!-- الهيدر -->
            <div class="bg-gradient-to-r from-primary to-secondary p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">سجل التدقيق #{{ $auditTrail->id }}</h2>
                        <p class="text-primary/80">{{ $auditTrail->ChangeDate->format('l, F j, Y - H:i:s') }}</p>
                    </div>
                    @php
                    $changeTypeColors = [
                    'I' => 'bg-green-500',
                    'U' => 'bg-yellow-500',
                    'D' => 'bg-red-500'
                    ];
                    $changeTypeText = [
                    'I' => 'إضافة',
                    'U' => 'تعديل',
                    'D' => 'حذف'
                    ];
                    @endphp
                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ $changeTypeColors[$auditTrail->ChangeType] }}">
                        {{ $changeTypeText[$auditTrail->ChangeType] }}
                    </span>
                </div>
            </div>

            <!-- محتوى البطاقة -->
            <div class="p-6 space-y-6">
                <!-- المعلومات الأساسية -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2"><i class="fas fa-table ml-2"></i> معلومات الجدول</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">اسم الجدول:</span>
                                <span class="font-medium">{{ $auditTrail->TableName }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">معرف السجل:</span>
                                <span class="font-medium">{{ $auditTrail->RecordID }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2"><i class="fas fa-calendar ml-2"></i> معلومات التوقيت</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">التاريخ:</span>
                                <span class="font-medium">{{ $auditTrail->ChangeDate->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">الوقت:</span>
                                <span class="font-medium">{{ $auditTrail->ChangeDate->format('H:i:s') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">منذ:</span>
                                <span class="font-medium">{{ $auditTrail->ChangeDate->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات المستخدم -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-user ml-2"></i> المستخدم المسؤول</h3>
                    @if($auditTrail->changedByUser)
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-primary rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ substr($auditTrail->changedByUser->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $auditTrail->changedByUser->name }}</p>
                            <p class="text-sm text-gray-600">{{ $auditTrail->changedByUser->email }}</p>
                            <p class="text-xs text-gray-500">ID: {{ $auditTrail->changedByUser->id }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-600">مستخدم غير معروف أو تم حذفه</p>
                    @endif
                </div>

                <!-- التفاصيل -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-info-circle ml-2"></i> تفاصيل العملية</h3>
                    <div class="bg-white p-4 rounded border">
                        <p class="text-gray-800 leading-relaxed">{{ $auditTrail->Details }}</p>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-cog ml-2"></i> معلومات إضافية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">حالة السجل:</span>
                            <span class="font-medium {{ $auditTrail->isActive ? 'text-green-600' : 'text-red-600' }}">
                                {{ $auditTrail->isActive ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">تاريخ الإنشاء:</span>
                            <span class="font-medium">{{ $auditTrail->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">آخر تحديث:</span>
                            <span class="font-medium">{{ $auditTrail->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الفوتر -->
            <div class="bg-gray-100 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">تم إنشاء السجل في {{ $auditTrail->created_at->format('Y-m-d H:i') }}</span>
                    <div class="flex space-x-3 space-x-reverse">
                        <a href="{{ route('audit-trails.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-right ml-2"></i> العودة
                        </a>
                        @can('delete', $auditTrail)
                        <form action="{{ route('audit-trails.destroy', $auditTrail->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash ml-2"></i> حذف السجل
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection