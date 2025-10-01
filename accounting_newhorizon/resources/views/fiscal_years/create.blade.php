@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <h1 class="text-2xl font-bold mb-6">إضافة سنة مالية</h1>
    <form action="{{ route('fiscal-years.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
        @csrf
        <div class="space-y-4">
            <div>
                <label>اسم السنة المالية</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label>تاريخ البداية</label>
                <input type="date" name="start_date" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label>تاريخ النهاية</label>
                <input type="date" name="end_date" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <input type="checkbox" name="is_closed" value="1">
                <label>مغلقة؟</label>

            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
            <a href="{{ route('fiscal-years.index') }}" class="px-4 py-2 border rounded">إلغاء</a>
            <button type="submit" class="px-4 py-2 bg-secondary text-primary rounded">حفظ</button>
        </div>
    </form>
</main>
@endsection