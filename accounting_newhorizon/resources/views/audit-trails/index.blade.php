@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-history ml-3 text-secondary"></i> مسارات التدقيق
        </h1>
        <div class="flex space-x-3 space-x-reverse">
            <button onclick="exportAuditTrails()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-file-export ml-2"></i> تصدير
            </button>
            <button onclick="showCleanupModal()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                <i class="fas fa-broom ml-2"></i> تنظيف السجلات
            </button>
        </div>
    </header>

    <!-- بطاقة الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-database text-blue-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistics['total']) }}</p>
                    <p class="text-sm text-gray-600">إجمالي السجلات</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-3">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistics['inserts']) }}</p>
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
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistics['updates']) }}</p>
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
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistics['deletes']) }}</p>
                    <p class="text-sm text-gray-600">عمليات الحذف</p>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة الفلاتر -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form id="filterForm" method="GET" action="{{ route('audit-trails.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary"
                    placeholder="ابحث في السجلات...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الجداول</label>
                <select name="table" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
                    <option value="">جميع الجداول</option>
                    @foreach($tables as $table)
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
                <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
            </div>

            <div class="md:col-span-5 flex justify-end space-x-3 space-x-reverse">
                <button type="submit" class="px-6 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                    <i class="fas fa-filter ml-2"></i> تطبيق الفلاتر
                </button>
                <a href="{{ route('audit-trails.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-redo ml-2"></i> إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- جدول مسارات التدقيق -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ والوقت</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الجداول</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع التغيير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التفاصيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditTrails as $trail)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $trail->ChangeDate->format('Y-m-d') }}</div>
                            <div class="text-sm text-gray-500">{{ $trail->ChangeDate->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $trail->TableName }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $trail->RecordID }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trail->changedByUser)
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-secondary to-primary rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ substr($trail->changedByUser->name, 0, 1) }}</span>
                                </div>
                                <div class="mr-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $trail->changedByUser->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $trail->changedByUser->email }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-sm text-gray-500">غير معروف</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $changeTypeColors = [
                            'I' => 'bg-green-100 text-green-800',
                            'U' => 'bg-yellow-100 text-yellow-800',
                            'D' => 'bg-red-100 text-red-800'
                            ];
                            $changeTypeText = [
                            'I' => 'إضافة',
                            'U' => 'تعديل',
                            'D' => 'حذف'
                            ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $changeTypeColors[$trail->ChangeType] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $changeTypeText[$trail->ChangeType] ?? $trail->ChangeType }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate">{{ $trail->Details }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('audit-trails.show', $trail->id) }}" class="text-secondary hover:text-primary ml-3">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            @can('delete', $trail)
                            <form action="{{ route('audit-trails.destroy', $trail->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <br>لا توجد سجلات تدقيق
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الترقيم -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $auditTrails->links() }}
        </div>
    </div>
</main>

<!-- Modal تنظيف السجلات -->
<div id="cleanupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">تنظيف السجلات القديمة</h3>
            <form id="cleanupForm" action="{{ route('audit-trails.cleanup') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">حذف السجلات الأقدم من (أيام)</label>
                    <input type="number" name="days" min="1" max="365" value="30"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary" required>
                </div>
                <div class="text-xs text-gray-500 mb-4">
                    سيتم حذف جميع سجلات التدقيق الأقدم من عدد الأيام المحدد. لا يمكن التراجع عن هذا الإجراء.
                </div>
                <div class="flex justify-center space-x-3 space-x-reverse">
                    <button type="button" onclick="hideCleanupModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        تأكيد التنظيف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function exportAuditTrails() {
        // بناء رابط التصدير مع الفلاتر الحالية
        const form = document.getElementById('filterForm');
        const action = form.getAttribute('action');
        const params = new URLSearchParams(new FormData(form));

        window.location.href = '{{ route("audit-trails.export") }}?' + params.toString();
    }

    function showCleanupModal() {
        document.getElementById('cleanupModal').classList.remove('hidden');
    }

    function hideCleanupModal() {
        document.getElementById('cleanupModal').classList.add('hidden');
    }

    // إغلاق Modal عند النقر خارجها
    window.onclick = function(event) {
        const modal = document.getElementById('cleanupModal');
        if (event.target === modal) {
            hideCleanupModal();
        }
    }

    // البحث التلقائي بعد تأخير
    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
</script>
@endsection