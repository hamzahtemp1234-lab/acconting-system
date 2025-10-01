@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-edit ml-3 text-secondary"></i> تعديل العميل
        </h1>
        <a href="{{ route('customers.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <form action="{{ route('customers.update',$customer->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- بيانات أساسية -->
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الكود</label>
                    <input type="text" value="{{ $customer->code }}" class="w-full border rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>


                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name',$customer->name) }}" class="w-full border rounded-lg px-4 py-2" required>
                    @error('name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">النوع</label>
                    <select name="type" class="w-full border rounded-lg px-4 py-2">
                        <option value="individual" {{ old('type',$customer->type)=='individual'?'selected':'' }}>فرد</option>
                        <option value="company" {{ old('type',$customer->type)=='company'?'selected':'' }}>شركة</option>
                    </select>
                    @error('type') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">الرقم الضريبي</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id',$customer->tax_id) }}" class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm mb-2">الهوية / الجواز</label>
                    <input type="text" name="id_number" value="{{ old('id_number',$customer->id_number) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <!-- اتصالات وعنوان -->
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone',$customer->phone) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">الجوال</label>
                    <input type="text" name="mobile" value="{{ old('mobile',$customer->mobile) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">البريد</label>
                    <input type="email" name="email" value="{{ old('email',$customer->email) }}" class="w-full border rounded-lg px-4 py-2">
                    @error('email') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">العنوان</label>
                    <input type="text" name="address" value="{{ old('address',$customer->address) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">المدينة</label>
                    <input type="text" name="city" value="{{ old('city',$customer->city) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">الدولة</label>
                    <input type="text" name="country" value="{{ old('country',$customer->country) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <!-- ربط محاسبي وتصنيف -->
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">العملة</label>
                    <select name="currency_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($currencies as $cur)
                        <option value="{{ $cur->id }}" {{ (string)old('currency_id',$customer->currency_id)==(string)$cur->id?'selected':'' }}>
                            {{ $cur->code }} - {{ $cur->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('currency_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <!-- <div>
                    <label class="block text-sm mb-2">الحساب في الدليل</label>
                    <select name="account_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                       {{-- @foreach($accounts as $acc)  --}}
                        <option value="{{-- $acc->id --}}" {{-- (string)old('account_id',$customer->account_id)==(string)$acc->id?'selected':'' --}}>
                            {{-- $acc->code --}} - {{-- $acc->name --}}
                        </option>
                        {{--  @endforeach --}}
                    </select>
                   {{--  @error('account_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror --}}
                </div> -->

                <div>
                    <label class="block text-sm mb-2">تصنيف العميل</label>
                    <select name="category_id" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)old('category_id',$customer->category_id)==(string)$cat->id?'selected':'' }}>
                            {{ $cat->code }} - {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- ائتمان ورصيد افتتاحي -->
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الحد الائتماني</label>
                    <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit',$customer->credit_limit) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">الرصيد الافتتاحي</label>
                    <input type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance',$customer->opening_balance) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">تاريخ الرصيد الافتتاحي</label>
                    <input type="date" name="opening_balance_date" value="{{ old('opening_balance_date', optional($customer->opening_balance_date)->format('Y-m-d')) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <!-- شروط دفع -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">شروط الدفع</label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms',$customer->payment_terms) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-2">طريقة الدفع المفضلة</label>
                    <select name="preferred_payment_method" class="w-full border rounded-lg px-4 py-2">
                        <option value="">—</option>
                        @php $ppm = old('preferred_payment_method',$customer->preferred_payment_method); @endphp
                        <option value="cash" {{ $ppm=='cash'?'selected':'' }}>نقد</option>
                        <option value="bank" {{ $ppm=='bank'?'selected':'' }}>تحويل بنكي</option>
                        <option value="cheque" {{ $ppm=='cheque'?'selected':'' }}>شيك</option>
                        <option value="card" {{ $ppm=='card'?'selected':'' }}>بطاقة</option>
                    </select>
                </div>
            </div>

            <!-- الحالة والتواريخ -->
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm mb-2">الحالة</label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center space-x-2 space-x-reverse">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active',$customer->is_active) ? 'checked' : '' }}>
                        <span>نشط</span>
                    </label>
                    @error('is_active') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
                <!-- <div>
                    <label class="block text-sm mb-2">تاريخ التسجيل</label>
                    <input type="date" name="registration_date" value="{{-- old('registration_date', optional($customer->registration_date)->format('Y-m-d')) --}}" class="w-full border rounded-lg px-4 py-2">
                </div> -->
            </div>

            <div>
                <label class="block text-sm mb-2">ملاحظات</label>
                <textarea name="notes" rows="4" class="w-full border rounded-lg px-4 py-2">{{ old('notes',$customer->notes) }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('customers.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit"
                    class="mr-3 px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</main>
@endsection