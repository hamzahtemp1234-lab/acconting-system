@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-3 text-secondary"></i> إضافة نوع مركز تكلفة
        </h1>
        <a href="{{ route('cost-center-types.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
            <form action="{{ route('cost-center-types.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            الرمز <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                            class="w-full border px-4 py-3 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition uppercase"
                            required>
                        @error('code')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            الاسم <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full border px-4 py-3 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                            required>
                        @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 space-x-reverse mt-8">
                    <a href="{{ route('cost-center-types.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">إلغاء</a>
                    <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg">
                        <i class="fas fa-save ml-2"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection