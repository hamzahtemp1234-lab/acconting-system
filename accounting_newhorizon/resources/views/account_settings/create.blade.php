@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-3 text-secondary"></i> إضافة إعداد حساب
        </h1>
        <a href="{{ route('account-settings.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('account-settings.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الوحدة <span class="text-red-500">*</span></label>
                    <select name="module" id="module" class="w-full border rounded-lg px-4 py-2" required>
                        @foreach($modules as $m => $keys)
                        <option value="{{ $m }}" {{ old('module')==$m?'selected':'' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('module') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">المفتاح <span class="text-red-500">*</span></label>
                    <select name="key" id="key" class="w-full border rounded-lg px-4 py-2" required>
                        @php
                        $selectedModule = old('module') ?? array_key_first($modules);
                        $keys = $modules[$selectedModule] ?? [];
                        $oldKey = old('key');
                        @endphp
                        @foreach($keys as $k)
                        <option value="{{ $k }}" {{ $oldKey==$k?'selected':'' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                    @error('key') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الحساب (إن وُجد)</label>
                    <select name="account_id" id="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ old('account_id')==$a->id?'selected':'' }}>
                            {{ $a->code }} - {{ $a->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

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

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">نوع النطاق</label>
                    <select name="scope_type" id="scope_type" class="w-full border rounded-lg px-4 py-2">
                        <option value="">عام</option>
                        <option value="currency" {{ old('scope_type')=='currency'?'selected':'' }}>عملة</option>
                        <option value="customer_category" {{ old('scope_type')=='customer_category'?'selected':'' }}>تصنيف عملاء</option>
                    </select>
                    @error('scope_type') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">القيمة حسب النطاق</label>
                    <select name="scope_id" id="scope_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @if(old('scope_type')==='currency')
                        @foreach($currencies as $c)
                        <option value="{{ $c->id }}" {{ (string)old('scope_id')===(string)$c->id?'selected':'' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                        @endforeach
                        @elseif(old('scope_type')==='customer_category')
                        @foreach($customerCategories as $cc)
                        <option value="{{ $cc->id }}" {{ (string)old('scope_id')===(string)$cc->id?'selected':'' }}>
                            {{ $cc->code ?? '' }} {{ $cc->code?'-':'' }} {{ $cc->name }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                    @error('scope_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('account-settings.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="mr-3 px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ
                </button>
            </div>
        </form>
    </div>
</main>

{{-- تبديل مفاتيح الوحدة والنطاق ديناميكي --}}
<script>
    const modules = @json($modules);
    const moduleSelect = document.getElementById('module');
    const keySelect = document.getElementById('key');
    const scopeType = document.getElementById('scope_type');
    const scopeId = document.getElementById('scope_id');

    const currencies = @json($currencies);
    const customerCategories = @json($customerCategories);

    moduleSelect.addEventListener('change', e => {
        const m = e.target.value;
        keySelect.innerHTML = '';
        (modules[m] || []).forEach(k => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = k;
            keySelect.appendChild(opt);
        });
    });

    scopeType.addEventListener('change', e => {
        scopeId.innerHTML = '';
        const t = e.target.value;
        const blank = document.createElement('option');
        blank.value = '';
        blank.textContent = '—';
        scopeId.appendChild(blank);

        if (t === 'currency') {
            currencies.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.code} - ${c.name}`;
                scopeId.appendChild(opt);
            });
        } else if (t === 'customer_category') {
            customerCategories.forEach(cc => {
                const opt = document.createElement('option');
                opt.value = cc.id;
                opt.textContent = `${cc.code ?? ''} ${cc.code?'-':''} ${cc.name}`;
                scopeId.appendChild(opt);
            });
        }
    });
</script>
@endsection