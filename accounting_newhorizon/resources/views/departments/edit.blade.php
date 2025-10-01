@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-pen-to-square ml-3 text-secondary"></i> تعديل قسم: {{ $department->name }}
        </h1>
        <a href="{{ route('departments.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('departments.update', $department) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            {{-- الصف 1 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الفرع <span class="text-red-500">*</span></label>
                    <select name="branch_id" class="w-full border rounded-lg px-4 py-2" required>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(old('branch_id', $department->branch_id)==$b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">رمز القسم <span class="text-red-500">*</span></label>
                    <input name="code" value="{{ old('code', $department->code) }}" maxlength="20"
                        class="w-full border rounded-lg px-4 py-2" readonly>
                    @error('code')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                    <p class="text-xs text-gray-500 mt-1">لضمان تناسق التسلسل داخل الفرع، الرمز للعرض فقط.</p>
                </div>

                <div>
                    <label class="block text-sm mb-2">اسم القسم <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $department->name) }}" maxlength="100"
                        class="w-full border rounded-lg px-4 py-2" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الصف 2 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">مدير القسم</label>
                    @isset($employees)
                    <select name="manager_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">— لا شيء —</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('manager_id', $department->manager_id)==$emp->id)>{{ $emp->code }} - {{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @else
                    <input name="manager_id" value="{{ old('manager_id', $department->manager_id) }}" class="w-full border rounded-lg px-4 py-2" placeholder="ID المدير (اختياري)">
                    @endisset
                    @error('manager_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الأزرار --}}
            <div class="flex justify-end">
                <a href="{{ route('departments.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
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