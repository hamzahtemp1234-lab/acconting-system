@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <!-- رأس الصفحة -->
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل العملة
        </h1>
        <a href="{{ route('currencies.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-4xl mx-auto space-y-8">

        <!-- ✅ فورم تعديل العملة -->
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
            <form id="updateForm" action="{{ route('currencies.update', $currency->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- رمز العملة -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            رمز العملة (ISO) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" name="code"
                            value="{{ old('code', $currency->code) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                                      focus:ring-secondary focus:border-secondary transition uppercase"
                            placeholder="مثال: SAR, USD, EUR"
                            maxlength="5" required>
                        @error('code')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اسم العملة -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            اسم العملة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', $currency->name) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                                      focus:ring-secondary focus:border-secondary transition"
                            placeholder="مثال: الريال السعودي، الدولار الأمريكي"
                            required>
                        @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- رمز العملة -->
                    <div>
                        <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                            رمز العملة
                        </label>
                        <input type="text" id="symbol" name="symbol"
                            value="{{ old('symbol', $currency->symbol) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                                      focus:ring-secondary focus:border-secondary transition"
                            placeholder="مثال: ﷼, $, €"
                            maxlength="10">
                        @error('symbol')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- معلومات إضافية -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">معلومات النظام:</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">تاريخ الإنشاء:</span>
                                <span class="font-medium">{{ $currency->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">آخر تحديث:</span>
                                <span class="font-medium">{{ $currency->updated_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">الحالة:</span>
                                <span class="font-medium {{ $currency->trashed() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $currency->trashed() ? 'محذوفة' : 'نشطة' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">عدد الاستخدامات:</span>
                                <span class="font-medium">{{ $currency->systemSettings->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- معاينة -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">معاينة العملة:</h4>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-600 
                                            rounded-xl flex items-center justify-center shadow-lg">
                                    <span id="previewCode" class="text-white font-bold text-lg">{{ $currency->code }}</span>
                                </div>
                                <div>
                                    <div id="previewName" class="text-xl font-bold text-gray-800">{{ $currency->name }}</div>
                                    <div id="previewSymbol" class="text-2xl font-semibold text-yellow-600 mt-1">
                                        {{ $currency->symbol ?? '---' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">مثال للعرض:</div>
                                <div class="text-lg font-semibold text-gray-800">
                                    <span id="previewExample">100.00 {{ $currency->symbol ?? $currency->code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('currencies.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 
                              transition font-medium text-center">
                        <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 
                                   transition font-bold shadow-lg shadow-secondary/30 flex items-center justify-center">
                        <i class="fas fa-save ml-2"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>

        <!-- 🟢 قسم إدارة أسعار الصرف -->
        <div class="bg-white rounded-xl shadow-lg border p-6">
            <h3 class="text-xl font-semibold text-primary mb-4">
                <i class="fas fa-dollar-sign ml-2 text-secondary"></i> أسعار الصرف
            </h3>

            <!-- جدول أسعار الصرف السابقة -->
            <div class="overflow-x-auto mb-6">
                <table class="w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-right">السعر</th>
                            <th class="px-4 py-2 text-right">تاريخ البداية</th>
                            <th class="px-4 py-2 text-right">تاريخ الإضافة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currency->exchangeRates as $rate)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $rate->rate }}</td>
                            <td class="px-4 py-2">{{ $rate->from_date_exchange }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $rate->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                لا توجد أسعار صرف لهذه العملة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- إضافة سعر صرف جديد -->
            <form action="{{ route('exchange-rates.store', $currency->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سعر الصرف</label>
                    <input type="number" step="0.0001" name="rate" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ بدء الصرف</label>
                    <input type="date" name="from_date_exchange" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary">
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium">
                    <i class="fas fa-plus ml-2"></i> إضافة سعر صرف
                </button>
            </form>
        </div>

    </div>
</main>

<script>
    // معاينة العملة أثناء الكتابة
    document.getElementById('code').addEventListener('input', function(e) {
        document.getElementById('previewCode').textContent = this.value.toUpperCase() || '---';
        updatePreviewExample();
    });

    document.getElementById('name').addEventListener('input', function(e) {
        document.getElementById('previewName').textContent = this.value || 'اسم العملة';
    });

    document.getElementById('symbol').addEventListener('input', function(e) {
        const symbol = this.value || document.getElementById('code').value;
        document.getElementById('previewSymbol').textContent = symbol;
        updatePreviewExample();
    });

    function updatePreviewExample() {
        const code = document.getElementById('code').value || '---';
        const symbol = document.getElementById('symbol').value || code;
        document.getElementById('previewExample').textContent = `100.00 ${symbol}`;
    }

    // تحقق من صحة البيانات عند الحفظ فقط
    document.getElementById('updateForm').addEventListener('submit', function(e) {
        const code = document.getElementById('code').value.trim();
        const name = document.getElementById('name').value.trim();

        if (!code || !name) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول الإلزامية');
            return;
        }

        if (code.length < 3) {
            e.preventDefault();
            alert('رمز العملة يجب أن يكون مكون من 3 أحرف على الأقل');
            return;
        }
    });
</script>
@endsection