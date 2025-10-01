@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-university ml-3 text-secondary"></i> إضافة بنك
        </h1>
        <a href="{{ route('banks.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded">
            @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('banks.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- الصف 1 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود (يتولد تلقائيًا)</label>
                    <input type="text" value="{{ $nextCode }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">اسم البنك <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-4 py-2" required>
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
                        <option value="{{ $c->id }}" @selected(old('currency_id')==$c->id)>{{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('currency_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm mb-2">الفرع</label>
                    <select name="branch_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm mb-2">الهاتف</label>
                    <input name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-4 py-2">
                    @error('phone')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- الصف 3 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">IBAN <br>( رقم الحساب البنكي الدولي)</label>
                    <input name="iban" value="{{ old('iban') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">SWIFT<br>(رمز تعريف البنك العالمي)</label>
                    <input name="swift" value="{{ old('swift') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">الشخص المسؤول</label>
                    <input name="contact_name" value="{{ old('contact_name') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            {{-- الصف 4 --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">العنوان</label>
                    <input name="address" value="{{ old('address') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active',1)?'checked':'' }}>
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
                        <option value="{{ $a->id }}" @selected(old('account_id')==$a->id)>{{ $a->code }} - {{ $a->name }}</option>
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

            <div>
                <label class="block text-sm mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('banks.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
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