@extends('layouts.app')

@section('content')
<main class="p-8">
    <h1 class="text-2xl font-bold mb-6">تعديل نوع الحساب</h1>

    <div class="max-w-xl mx-auto bg-white shadow rounded-lg p-6">
        <form action="{{ route('account-types.update', $type->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- اسم النوع -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    اسم النوع <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $type->name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary"
                    placeholder="أدخل اسم النوع" required>
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- طبيعة الحساب -->
            <div class="mb-4">
                <label for="nature" class="block text-sm font-medium text-gray-700 mb-2">
                    طبيعة الحساب <span class="text-red-500">*</span>
                </label>
                <select id="nature" name="nature"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary" required>
                    <option value="debit" {{ old('nature', $type->nature) == 'debit' ? 'selected' : '' }}>مدين</option>
                    <option value="credit" {{ old('nature', $type->nature) == 'credit' ? 'selected' : '' }}>دائن</option>
                </select>
                @error('nature')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- حالة التفعيل -->
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox"
                        {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                    <span class="ml-2">نشط</span>
                </label>
            </div>

            <!-- الأزرار -->
            <div class="flex justify-between">
                <a href="{{ route('account-types.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">إلغاء</a>
                <button type="submit"
                    class="px-6 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold">
                    <i class="fas fa-save ml-2"></i> تحديث
                </button>
            </div>
        </form>
    </div>
</main>
@endsection