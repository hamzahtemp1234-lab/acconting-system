@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-history ml-3 text-secondary"></i> سجل النشاطات الشخصية
        </h1>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
                <i class="fas fa-arrow-right ml-2"></i> العودة للبروفايل
            </a>
            <button onclick="exportActivityLog()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-file-export ml-2"></i> تصدير السجل
            </button>
        </div>
    </header>

    <!-- بطاقة الإحصائيات الشخصية -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-list-alt text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($auditLogs->total()) }}</p>
                    <p class="text-sm text-gray-600">إجمالي النشاطات</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($userStats['inserts'] ?? 0) }}</p>
                    <p class="text-sm text-gray-600">عمليات الإضافة</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-edit text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($userStats['updates'] ?? 0) }}</p>
                    <p class="text-sm text-gray-600">عمليات التعديل</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($userStats['deletes'] ?? 0) }}</p>
                    <p class="text-sm text-gray-600">عمليات الحذف</p>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المستخدم -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center space-x-4 space-x-reverse">
            @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="صورة البروفايل"
                class="w-16 h-16 rounded-full border-2 border-secondary">
            @else
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-secondary to-primary flex items-center justify-center">
                <span class="text-white text-xl font-bold">{{ substr($user->name, 0, 1) }}</span>
            </div>
            @endif
            <div>
                <h3 class="text-xl font-semibold text-primary">{{ $user->name }}</h3>
                <p class="text-gray-600">{{ $user->email }}</p>
                <p class="text-sm text-gray-500">آخر نشاط: {{ $lastActivity ?? 'غير متوفر' }}</p>
            </div>
        </div>
    </div>

    <!-- بطاقة الفلاتر -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form id="filterForm" method="GET" action="{{ route('profile.activity-log') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">بحث في التفاصيل</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary"
                    placeholder="ابحث في النشاطات...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الجداول</label>
                <select name="table" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
                    <option value="">جميع الجداول</option>
                    @foreach($userTables as $table)
                    <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>{{ $table }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع التغيير</label>
                <select name="change_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
                    <option value="">جميع الأنواع</option>
                    <option value="I" {{ request('change_type') == 'I' ? 'selected' : '' }}>إضافة</option>
                    <option value="U" {{ request('change_type') == 'U' ? 'selected' : '' }}>تعديل</option>
                    <option value="D" {{ request('change_type') == 'D' ? 'selected' : '' }}>حذف</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الفترة الزمنية</label>
                <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
                    <option value="">جميع الفترات</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>اليوم</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>أسبوع</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>شهر</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>سنة</option>
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end space-x-3 space-x-reverse">
                <button type="submit" class="px-6 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                    <i class="fas fa-filter ml-2"></i> تطبيق الفلاتر
                </button>
                <a href="{{ route('profile.activity-log') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-redo ml-2"></i> إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- جدول النشاطات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ والوقت</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الجداول</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع التغيير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التفاصيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">معرف السجل</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->ChangeDate->format('Y-m-d') }}</div>
                            <div class="text-sm text-gray-500">{{ $log->ChangeDate->format('H:i:s') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->ChangeDate->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                    @if($log->TableName == 'users') bg-purple-100 text-purple-600
                                    @elseif($log->TableName == 'roles') bg-blue-100 text-blue-600
                                    @elseif($log->TableName == 'permissions') bg-green-100 text-green-600
                                    @else bg-gray-100 text-gray-600 @endif">
                                    <i class="fas 
                                        @if($log->TableName == 'users') fa-users
                                        @elseif($log->TableName == 'roles') fa-user-tag
                                        @elseif($log->TableName == 'permissions') fa-shield-alt
                                        @else fa-table @endif text-xs">
                                    </i>
                                </div>
                                <div class="mr-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->TableName }}</div>
                                    <div class="text-xs text-gray-500">IP: {{ $log->IPAddress ?? 'غير معروف' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $changeTypeColors = [
                            'I' => 'bg-green-100 text-green-800 border-green-200',
                            'U' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'D' => 'bg-red-100 text-red-800 border-red-200'
                            ];
                            $changeTypeIcons = [
                            'I' => 'fa-plus-circle',
                            'U' => 'fa-edit',
                            'D' => 'fa-trash'
                            ];
                            $changeTypeText = [
                            'I' => 'إضافة',
                            'U' => 'تعديل',
                            'D' => 'حذف'
                            ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $changeTypeColors[$log->ChangeType] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                <i class="fas {{ $changeTypeIcons[$log->ChangeType] ?? 'fa-info-circle' }} ml-1 text-xs"></i>
                                {{ $changeTypeText[$log->ChangeType] ?? $log->ChangeType }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $log->ChangeDescription }}</div>
                            @if($log->Details && $log->Details != $log->ChangeDescription)
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($log->Details, 100) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $log->RecordID }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">لا توجد نشاطات مسجلة</p>
                                <p class="text-gray-400 text-sm mt-2">سيظهر هنا سجل نشاطاتك في النظام</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الترقيم -->
        @if($auditLogs->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $auditLogs->links() }}
        </div>
        @endif
    </div>

    <!-- ملخص النشاطات -->
    @if($auditLogs->count() > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- أكثر الجداول تفاعلاً -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-primary mb-4 border-b pb-2">
                <i class="fas fa-chart-pie ml-2 text-secondary"></i> أكثر الجداول تفاعلاً
            </h3>
            <div class="space-y-3">
                @foreach($topTables as $table => $count)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $table }}</span>
                    <span class="text-sm font-medium text-primary">{{ $count }} نشاط</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- توزيع النشاطات الزمني -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-primary mb-4 border-b pb-2">
                <i class="fas fa-chart-line ml-2 text-secondary"></i> النشاط خلال الأسبوع
            </h3>
            <div class="space-y-3">
                @foreach($weeklyActivity as $day => $count)
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 w-20">{{ $day }}</span>
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-secondary h-2 rounded-full" style="width: {{ ($count / max($weeklyActivity)) * 100 }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-primary w-8 text-left">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</main>

<script>
    function exportActivityLog() {
        // بناء رابط التصدير مع الفلاتر الحالية
        const form = document.getElementById('filterForm');
        const params = new URLSearchParams(new FormData(form));

        // إضافة معلمة التصدير
        params.append('export', 'true');

        window.location.href = '{{ route("profile.activity-log") }}?' + params.toString();
    }

    // البحث التلقائي بعد تأخير
    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });

    // تحديث الفلاتر تلقائياً عند تغيير بعضها
    document.querySelector('select[name="period"]').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
@endsection