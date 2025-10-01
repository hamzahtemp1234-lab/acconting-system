@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-user-plus ml-3 text-secondary"></i> إضافة وكيل
        </h1>
        <a href="{{ route('agents.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100">
            <i class="fas fa-arrow-right ml-2"></i> العودة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('agents.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود (يتولد تلقائياً)</label>
                    <input type="text" value="{{ $nextCode }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الاسم <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">النوع</label>
                    <select name="type" class="w-full border rounded-lg px-4 py-2">
                        <option value="individual" {{ old('type')==='company'?'':'selected' }}>فرد</option>
                        <option value="company" {{ old('type')==='company'?'selected':'' }}>شركة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2">العمولة %</label>
                    <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ old('commission_rate', 0) }}" class="w-full border rounded-lg px-4 py-2">
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

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">العملة</label>
                    <select name="currency_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($currencies as $c)
                        <option value="{{ $c->id }}" {{ (string)old('currency_id')===(string)$c->id?'selected':'' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الحساب المرتبط (اختيار حساب جاهز)</label>
                    <select name="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ (string)old('account_id')===(string)$a->id?'selected':'' }}>
                            {{ $a->code }} - {{ $a->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">أو اختر أب مجموعة بالأسفل لتوليد حساب فرعي تلقائي</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-3">
                    <label class="block text-sm mb-2">إنشاء حساب فرعي تلقائي تحت هذا الأب (مجموعة)</label>
                    <select name="parent_account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($parentGroups as $g)
                        <option value="{{ $g->id }}" {{ (string)old('parent_account_id')===(string)$g->id?'selected':'' }}>
                            {{ $g->code }} - {{ $g->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">رقم ضريبي</label>
                    <input name="tax_id" value="{{ old('tax_id') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">هوية/جواز</label>
                    <input name="id_number" value="{{ old('id_number') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div><label class="block text-sm mb-2">الهاتف</label><input name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm mb-2">الجوال</label><input name="mobile" value="{{ old('mobile') }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm mb-2">المدينة</label><input name="city" value="{{ old('city') }}" class="w-full border rounded-lg px-4 py-2"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div><label class="block text-sm mb-2">الدولة</label><input name="country" value="{{ old('country') }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div>
                    <label class="block text-sm mb-2">العنوان</label>
                    <input name="address" value="{{ old('address') }}" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div>
                <label class="block text-sm mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('agents.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
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