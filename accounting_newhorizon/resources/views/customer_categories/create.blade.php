@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-3 text-secondary"></i> إضافة تصنيف جديد
        </h1>
        <a href="{{ route('customer-categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('customer-categories.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block mb-2">الكود</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded-lg px-4 py-2" required>
                </div>
                <div>
                    <label class="block mb-2">الاسم</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">الحساب المرتبط</label>
                    <select name="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ (string)old('account_id', $category->account_id ?? '') === (string)$acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block mb-2">الوصف</label>
                    <textarea name="description" class="w-full border rounded-lg px-4 py-2">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label for="isActive" class="block text-sm font-medium text-gray-700 mb-1">حالة التصنيف</label>
                    <select id="isActive" name="is_active"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition bg-white">
                        <option value="1" {{ old('is_active', 1) ? 'selected' : '' }}>نشط (يمكن استخدامه)</option>
                        <option value="0" {{ !old('is_active', 1) ? 'selected' : '' }}>غير نشط (معلق)</option>
                    </select>
                    @error('isActive')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-secondary text-primary rounded-lg">حفظ</button>
            </div>
        </form>
    </div>
</main>
@endsection