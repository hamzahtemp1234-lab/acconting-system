@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-6 pb-4 border-b">
        <h1 class="text-2xl font-extrabold text-primary">
            <i class="fas fa-plus-circle ml-2 text-secondary"></i> إضافة مركز تكلفة
        </h1>
        <a href="{{ route('cost-centers.index') }}" class="px-4 py-2 border rounded-lg text-primary hover:bg-gray-100">🔙 رجوع</a>
    </header>

    <div class="bg-white rounded-xl shadow-xl p-8">
        <form action="{{ route('cost-centers.store') }}" method="POST">
            @csrf
            @include('cost-centers._form')

            <div class="flex justify-end mt-6 space-x-4 space-x-reverse">
                <a href="{{ route('cost-centers.index') }}" class="px-6 py-3 border rounded-lg text-gray-600 hover:bg-gray-100">إلغاء</a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold">💾 حفظ</button>
            </div>
        </form>
    </div>
</main>
@endsection