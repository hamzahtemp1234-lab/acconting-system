@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-key ml-3 text-secondary"></i> تغيير كلمة المرور
        </h1>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
                <i class="fas fa-arrow-right ml-2"></i> العودة للبروفايل
            </a>
        </div>
    </header>

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl border-t-4 border-primary">

        <form action="{{ route('profile.change-password.update') }}" method="POST" class="space-y-6">
            @csrf

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 ml-2"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 ml-2"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        كلمة المرور الحالية <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="current_password" name="current_password" type="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition pr-10 @error('current_password') border-red-500 @enderror"
                            placeholder="أدخل كلمة المرور الحالية">
                        <button type="button" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        كلمة المرور الجديدة <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition pr-10 @error('password') border-red-500 @enderror"
                            placeholder="أدخل كلمة المرور الجديدة (6 أحرف على الأقل)">
                        <button type="button" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        تأكيد كلمة المرور الجديدة <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition pr-10"
                            placeholder="أعد إدخال كلمة المرور الجديدة">
                        <button type="button" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 ml-2"></i>
                    <p class="text-blue-700 text-sm">
                        <strong>نصائح لأمان أفضل:</strong> استخدم مزيجاً من الأحرف الكبيرة والصغيرة، الأرقام، والرموز الخاصة.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-4 space-x-reverse">
                <a href="{{ route('profile.show') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-bold shadow-lg shadow-secondary/30">
                    <i class="fas fa-save ml-2"></i> تغيير كلمة المرور
                </button>
            </div>
        </form>

    </div>
</main>

<script>
    // إظهار/إخفاء كلمة المرور
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentNode.querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });

    // التحقق من تطابق كلمات المرور
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('كلمات المرور غير متطابقة! يرجى التأكد من تطابق كلمة المرور الجديدة وتأكيدها.');
        }
    });
</script>
@endsection