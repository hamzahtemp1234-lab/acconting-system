@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-user-plus ml-3 text-secondary"></i> إضافة مورد جديد
        </h1>
        <a href="{{ route('suppliers.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود (يُولّد تلقائيًا)</label>
                    <input type="text" value="{{ $nextCode }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                    <p class="text-xs text-gray-500 mt-1">سيتم حفظ الكود تلقائيًا عند الإنشاء.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">البريد</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-4 py-2">
                    @error('email') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-2">تصنيف المورد</label>
                    <select name="category_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>
                            {{ $cat->code }} - {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                    <p class="text-xs text-gray-500 mt-1">إن كان للتصنيف حساب أب «مجموعة»، سينشأ حساب مورد تلقائي تحته.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active',1) ? 'checked' : '' }}>
                        <span>نشط</span>
                    </label>
                    @error('is_active') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('suppliers.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="mr-3 px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ
                </button>
            </div>
        </form>
    </div>

</main>
@endsection