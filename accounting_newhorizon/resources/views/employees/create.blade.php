@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-user-plus ml-3 text-secondary"></i> إضافة موظف
        </h1>
        <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('employees.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- الصف 1 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">رقم/رمز الموظف (يتولد تلقائياً)</label>
                    <input type="text" value="{{ $nextCode ?? '' }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                    <p class="text-xs text-gray-500 mt-1">سيتم توليده تلقائيًا عند الحفظ.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الاسم <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" maxlength="255" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الصف 2 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الإدارة</label>
                    <select name="department_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">— لا شيء —</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الحساب المحاسبي</label>
                    <select name="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">— لا شيء —</option>
                        @foreach($accounts as $a)
                        <option value="{{ $a->id }}" @selected(old('account_id')==$a->id)>{{ $a->code }} - {{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('account_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الصف 3 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الهاتف</label>
                    <input name="phone" value="{{ old('phone') }}" maxlength="50" class="w-full border rounded-lg px-4 py-2">
                    @error('phone')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">البريد الإلكتروني</label>
                    <input name="email" type="email" value="{{ old('email') }}" maxlength="100" class="w-full border rounded-lg px-4 py-2">
                    @error('email')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الأزرار --}}
            <div class="flex justify-end">
                <a href="{{ route('employees.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button class="mr-3 px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ
                </button>
            </div>
        </form>
    </div>
</main>
@endsection