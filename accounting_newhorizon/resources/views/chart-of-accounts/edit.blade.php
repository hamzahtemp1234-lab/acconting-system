@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل الحساب
        </h1>
        <a href="{{ route('chart-of-accounts.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-2xl border-t-4 border-primary p-8">
        @if(session('warning'))
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4">
            {{ session('warning') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        <form action="{{ route('chart-of-accounts.update', $account->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">

                <!-- رقم الحساب -->
                <div>
                    <input type="hidden" name="view" value="{{ request('view', 'table') }}">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">رقم الحساب</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $account->code) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>

                <!-- اسم الحساب -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الحساب <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
                        required>
                </div>

                <!-- هل هو مجموعة -->
                <div class="flex items-center">
                    <input type="checkbox" id="is_group" name="is_group" value="1"
                        class="h-5 w-5 text-secondary border-gray-300 rounded"
                        {{ old('is_group', $account->is_group) ? 'checked' : '' }}>
                    <label for="is_group" class="mr-2 text-sm font-medium text-gray-700">هل هو مجموعة؟</label>
                </div>
                <!-- الحساب الأب -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">الحساب الأب</label>
                    <select id="parent_id" name="parent_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        <option value="">— لا يوجد (حساب رئيسي) —</option>
                        @foreach($parentAccounts as $parent)
                        <option value="{{ $parent->id }}"
                            {{ old('parent_id', $account->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->code }} - {{ $parent->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- نوع الحساب -->
                <div id="accountTypeWrapper" class="{{ old('is_group', $account->is_group) ? 'hidden' : '' }}">
                    <label for="account_type_id" class="block text-sm font-medium text-gray-700 mb-2">نوع الحساب</label>
                    <select id="account_type_id" name="account_type_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        <option value="">اختر نوع الحساب</option>
                        @foreach($accountTypes as $type)
                        <option value="{{ $type->id }}" data-nature="{{ $type->nature }}"
                            {{ old('account_type_id', $account->account_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->nature }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- الطبيعة -->
                <div id="natureWrapper" class="{{ old('is_group', $account->is_group) ? '' : 'hidden' }}">
                    <label for="nature" class="block text-sm font-medium text-gray-700 mb-2">طبيعة الحساب</label>
                    <select id="nature" name="nature"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        <option value="">اختر الطبيعة</option>
                        <option value="debit" {{ old('nature', $account->nature) == 'debit' ? 'selected' : '' }}>مدين</option>
                        <option value="credit" {{ old('nature', $account->nature) == 'credit' ? 'selected' : '' }}>دائن</option>
                    </select>
                </div>

                <!-- العملة -->
                <div>
                    <label for="currency_id" class="block text-sm font-medium text-gray-700 mb-2">العملة</label>
                    <select id="currency_id" name="currency_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" {{ old('currency_id', $account->currency_id) == $currency->id ? 'selected' : '' }}>
                            {{ $currency->name }} ({{ $currency->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- الحالة -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select id="status" name="status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        <option value="نشط" {{ old('status', $account->status) == 'نشط' ? 'selected' : '' }}>نشط</option>
                        <option value="غير نشط" {{ old('status', $account->status) == 'غير نشط' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

            </div>

            <!-- الأزرار -->
            <div class="flex justify-end mt-8 space-x-4 space-x-reverse">
                <a href="{{ route('chart-of-accounts.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">إلغاء</a>
                <button type="submit"
                    class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold">
                    <i class="fas fa-save ml-2"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    const isGroupCheckbox = document.getElementById('is_group');
    const accountTypeWrapper = document.getElementById('accountTypeWrapper');
    const natureWrapper = document.getElementById('natureWrapper');
    const accountTypeSelect = document.getElementById('account_type_id');
    const natureSelect = document.getElementById('nature');

    function toggleFields() {
        const isGroup = isGroupCheckbox.checked;
        accountTypeWrapper.classList.toggle('hidden', isGroup);
        natureWrapper.classList.toggle('hidden', !isGroup);

        if (!isGroup) {
            updateNature();
        }
    }

    function updateNature() {
        const selected = accountTypeSelect.options[accountTypeSelect.selectedIndex];
        const nature = selected ? selected.getAttribute('data-nature') : '';
        natureSelect.value = nature || '';
    }

    isGroupCheckbox.addEventListener('change', toggleFields);
    accountTypeSelect.addEventListener('change', updateNature);

    toggleFields();
</script>
@endsection