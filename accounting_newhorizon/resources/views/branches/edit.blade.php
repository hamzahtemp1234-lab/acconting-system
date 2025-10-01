@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-pen-to-square ml-3 text-secondary"></i> تعديل فرع: {{ $branch->name }}
        </h1>
        <a href="{{ route('branches.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('branches.update', $branch) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            {{-- الصف 1 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">رمز الفرع <span class="text-red-500">*</span></label>
                    <input name="code" value="{{ old('code', $branch->code) }}" maxlength="20"
                        class="w-full border rounded-lg px-4 py-2" readonly>
                    @error('code')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">اسم الفرع <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $branch->name) }}" maxlength="100"
                        class="w-full border rounded-lg px-4 py-2" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الصف 2 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">العنوان</label>
                    <input name="address" value="{{ old('address', $branch->address) }}" maxlength="255"
                        class="w-full border rounded-lg px-4 py-2">
                    @error('address')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm mb-2">الهاتف</label>
                    <input name="phone" value="{{ old('phone', $branch->phone) }}" maxlength="50"
                        class="w-full border rounded-lg px-4 py-2">
                    @error('phone')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الحالة --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $branch->is_active))>
                        <span>نشط</span>
                    </label>
                </div>
            </div>

            {{-- الأزرار --}}
            <div class="flex justify-end">
                <a href="{{ route('branches.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
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