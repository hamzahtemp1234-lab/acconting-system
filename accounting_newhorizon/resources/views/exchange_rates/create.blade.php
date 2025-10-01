@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">إضافة سعر صرف لـ {{ $currency->name }} ({{ $currency->code }})</h2>

        <form action="{{ route('exchange-rates.store', $currency->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2">سعر الصرف</label>
                <input type="number" step="0.0001" name="rate"
                    class="w-full border rounded px-4 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block mb-2">تاريخ بدء الصرف</label>
                <input type="date" name="from_date_exchange"
                    class="w-full border rounded px-4 py-2" required>
            </div>

            <button type="submit" class="px-6 py-2 bg-secondary text-primary rounded-lg">حفظ</button>
            <a href="{{ route('currencies.index') }}" class="ml-4 text-gray-600">إلغاء</a>
        </form>
    </div>
</main>
@endsection