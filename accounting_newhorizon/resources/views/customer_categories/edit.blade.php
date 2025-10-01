@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل التصنيف
        </h1>
        <a href="{{ route('customer-categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('customer-categories.update', $category->id) }}" method="POST">

            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label>الكود</label>
                    <input type="text" name="code" value="{{ old('code',$category->code) }}">
                    @error('code')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2">الاسم</label>
                    <input type="text" name="name" value="{{ old('name',$category->name) }}" class="w-full border rounded-lg px-4 py-2" required>
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
                    <textarea name="description" class="w-full border rounded-lg px-4 py-2">{{ old('description',$category->description) }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <span>نشط</span>
                    @error('is_active')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>

            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-secondary text-primary rounded-lg">تحديث</button>
            </div>
        </form>
    </div>
</main>
@endsection