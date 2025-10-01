@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-pen-to-square ml-3 text-secondary"></i> تعديل تصنيف: {{ $category->name }}
        </h1>
        <a href="{{ route('supplier-categories.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-8">
        @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded">
            @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('supplier-categories.update', $category) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود <span class="text-red-500">*</span></label>
                    <input name="code" value="{{ old('code', $category->code) }}" maxlength="20"
                        class="w-full border rounded-lg px-4 py-2" required>
                    @error('code')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الاسم <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $category->name) }}" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('description', $category->description) }}</textarea>
                @error('description')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-2">الحساب المرتبط (اختياري)</label>
                    <select name="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">— لا شيء —</option>
                        @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ (string)old('account_id', $category->account_id) === (string)$a->id ? 'selected' : '' }}>
                            {{ $a->code }} - {{ $a->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('account_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                        <span>نشط</span>
                    </label>
                    @error('is_active')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('supplier-categories.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button class="mr-3 px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> تحديث
                </button>
            </div>
        </form>
    </div>
</main>
@endsection