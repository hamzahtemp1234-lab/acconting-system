@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-3 text-secondary"></i> إضافة عملة جديدة
        </h1>
        <a href="{{ route('currencies.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
            <form action="{{ route('currencies.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- رمز العملة -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            رمز العملة (ISO) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition uppercase"
                            placeholder="مثال: SAR, USD, EUR"
                            maxlength="5" required>
                        <p class="text-xs text-gray-500 mt-1">يجب أن يكون الرمز مكون من 3 أحرف حسب معيار ISO</p>
                        @error('code')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اسم العملة -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            اسم العملة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
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
                        <input type="text" id="symbol" name="symbol" value="{{ old('symbol') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                            placeholder="مثال: ﷼, $, €"
                            maxlength="10">
                        <p class="text-xs text-gray-500 mt-1">الرمز المستخدم لعرض العملة</p>
                        @error('symbol')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- معاينة العملة -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">معاينة العملة:</h4>
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center">
                                <span id="previewCode" class="text-white font-bold text-sm">---</span>
                            </div>
                            <div>
                                <div id="previewName" class="text-lg font-semibold text-gray-800">اسم العملة</div>
                                <div id="previewSymbol" class="text-gray-600">---</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 space-x-reverse mt-8">
                    <a href="{{ route('currencies.index') }}" class="px-8 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-center">
                        <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                    <button type="submit" class="px-8 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30 flex items-center justify-center">
                        <i class="fas fa-save ml-2"></i> حفظ العملة
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    // معاينة العملة أثناء الكتابة
    document.getElementById('code').addEventListener('input', function(e) {
        document.getElementById('previewCode').textContent = this.value.toUpperCase() || '---';
    });

    document.getElementById('name').addEventListener('input', function(e) {
        document.getElementById('previewName').textContent = this.value || 'اسم العملة';
    });

    document.getElementById('symbol').addEventListener('input', function(e) {
        document.getElementById('previewSymbol').textContent = this.value || '---';
    });

    // التحقق من صحة البيانات قبل الإرسال
    document.querySelector('form').addEventListener('submit', function(e) {
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