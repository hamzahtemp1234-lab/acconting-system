@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-pen-to-square ml-3 text-secondary"></i> تعديل صندوق: {{ $cashbox->name }}
        </h1>
        <a href="{{ route('cash-boxes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded">
            @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('cash-boxes.update', $cashbox) }}" method="POST">
            @csrf @method('PUT')

            {{-- الصف 1 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود</label>
                    <input value="{{ $cashbox->code }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">اسم الصندوق <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $cashbox->name) }}" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الصف 2 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">العملة</label>
                    <select name="currency_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($currencies as $c)
                        <option value="{{ $c->id }}" @selected(old('currency_id', $cashbox->currency_id)==$c->id)>{{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2">الفرع</label>
                    <select name="branch_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(old('branch_id', $cashbox->branch_id)==$b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2">أمين الصندوق</label>
                    <select name="keeper_employee_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($keepers as $k)
                        <option value="{{ $k->id }}" @selected(old('keeper_employee_id', $cashbox->keeper_employee_id)==$k->id)>{{ $k->code }} - {{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- الصف 3 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">ملاحظات</label>
                    <input name="notes" value="{{ old('notes', $cashbox->notes) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $cashbox->is_active))>
                        <span>نشط</span>
                    </label>
                </div>
            </div>

            {{-- الحساب المحاسبي --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-2">ربط بحساب جاهز</label>
                    <select name="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($accounts as $a)
                        <option value="{{ $a->id }}" @selected(old('account_id', $cashbox->account_id)==$a->id)>{{ $a->code }} - {{ $a->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">إن اخترت حسابًا هنا، لن نقوم بالتوليد التلقائي.</p>
                </div>
                <div>
                    <label class="block text-sm mb-2">توليد تحت حساب أب (مجموعة)</label>
                    <select name="parent_account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($parentGroups as $g)
                        <option value="{{ $g->id }}" @selected(old('parent_account_id')==$g->id)>{{ $g->code }} - {{ $g->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">لو لم تختر شيئًا، سيتم الرجوع لإعدادات النظام (إن كانت مفعّلة).</p>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('cash-boxes.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
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