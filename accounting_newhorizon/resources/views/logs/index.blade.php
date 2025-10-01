@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">سجل النشاطات (Logs)</h1>
        <div class="flex space-x-2 space-x-reverse">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
                <i class="fas fa-download ml-2"></i> تصدير
            </button>
            <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium">
                <i class="fas fa-trash ml-2"></i> مسح السجلات
            </button>
        </div>
    </header>

    <section class="bg-white p-6 rounded-xl shadow-lg mb-8 search-section">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="search-input-container">
                <input type="text" placeholder="البحث في الرسائل" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
                <i class="fas fa-search"></i>
            </div>

            <div>
                <select class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
                    <option>جميع المستويات</option>
                    <option>INFO</option>
                    <option>WARNING</option>
                    <option>ERROR</option>
                    <option>DEBUG</option>
                </select>
            </div>

            <div>
                <select class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
                    <option>جميع المستخدمين</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="date" class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition">
            </div>
        </div>
    </section>

    <section class="bg-white p-6 rounded-xl shadow-lg table-container">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary/10">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">التاريخ والوقت</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">المستوى</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">الرسالة</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $log->Timestamp->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                            {{ $log->LogLevel == 'ERROR' ? 'bg-red-100 text-red-800' : 
                               ($log->LogLevel == 'WARNING' ? 'bg-yellow-100 text-yellow-800' : 
                               ($log->LogLevel == 'INFO' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ $log->LogLevel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $log->user ? $log->user->name : 'System' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-md truncate" title="{{ $log->Message }}">
                            {{ $log->Message }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </section>
</main>
@endsection