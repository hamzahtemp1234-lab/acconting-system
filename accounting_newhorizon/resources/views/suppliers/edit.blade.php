@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل المورد
        </h1>
        <a href="{{ route('suppliers.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود</label>
                    <input type="text" value="{{ $supplier->code }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name',$supplier->name) }}" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone',$supplier->phone) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">البريد</label>
                    <input type="email" name="email" value="{{ old('email',$supplier->email) }}" class="w-full border rounded-lg px-4 py-2">
                    @error('email') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-2">تصنيف المورد</label>
                    <select name="category_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)old('category_id',$supplier->category_id)===(string)$cat->id?'selected':'' }}>
                            {{ $cat->code }} - {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                    <p class="text-xs text-gray-500 mt-1">عند تغيير التصنيف إلى أبّ له حساب «مجموعة»، سننقل حساب المورد تحته بكود جديد.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active',$supplier->is_active) ? 'checked' : '' }}>
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
                    <i class="fas fa-save ml-2"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>

</main>
@endsection