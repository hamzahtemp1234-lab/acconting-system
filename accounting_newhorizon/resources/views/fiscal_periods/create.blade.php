@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <!-- رأس الصفحة -->
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-3 text-secondary"></i> إضافة فترة مالية جديدة
        </h1>
        <a href="{{ route('fiscal-periods.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
            <form action="{{ route('fiscal-periods.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- السنة المالية -->
                    <div>
                        <label for="fiscal_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                            السنة المالية <span class="text-red-500">*</span>
                        </label>
                        <select id="fiscal_year_id" name="fiscal_year_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                            required>
                            <option value="">اختر السنة</option>
                            @foreach($fiscalYears as $year)
                            <option value="{{ $year->id }}" {{ old('fiscal_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('fiscal_year_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- رقم الفترة -->
                    <div>
                        <label for="period_no" class="block text-sm font-medium text-gray-700 mb-2">
                            رقم الفترة (1-13) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="period_no" name="period_no" min="1" max="13"
                            value="{{ old('period_no') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                            required>
                        @error('period_no')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- تاريخ البداية -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ البداية <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                            required>
                        @error('start_date')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- تاريخ النهاية -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ النهاية <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                            required>
                        @error('end_date')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- حالة الإغلاق -->
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" id="is_closed" name="is_closed" value="1"
                            class="w-5 h-5 text-secondary focus:ring-secondary border-gray-300 rounded"
                            {{ old('is_closed') ? 'checked' : '' }}>
                        <label for="is_closed" class="text-sm text-gray-700">مغلقة</label>
                    </div>
                </div>

                <!-- الأزرار -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('fiscal-periods.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium">
                        <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                        <i class="fas fa-save ml-2"></i> حفظ الفترة
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection