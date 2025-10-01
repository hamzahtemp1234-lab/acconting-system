<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - النظام المحاسبي المتكامل</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-color: #05205a;
            --secondary-color: #f5af22;
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .text-primary {
            color: var(--primary-color);
        }

        .bg-secondary {
            background-color: var(--secondary-color);
        }

        .text-secondary {
            color: var(--secondary-color);
        }

        .visual-bg {
            background-image: url('{{ asset("images/accounting-abstract-bg.jpg") }}');
            /* ضع الصورة داخل public/images */
            background-size: cover;
            background-position: center;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="h-screen flex antialiased">
    <!-- القسم الأيسر (الجانب البصري) -->
    <div class="hidden lg:flex lg:w-1/2 visual-bg justify-center items-center relative">
        <div class="absolute inset-0 bg-primary opacity-80"></div>
        <div class="text-white text-center p-12 relative z-10">
            <i class="fas fa-chart-line text-6xl text-secondary mb-4"></i>
            <h2 class="text-4xl font-extrabold mb-3">النظام المحاسبي المتكامل</h2>
            <p class="text-lg opacity-90">إدارة مالية دقيقة وموثوقة لجميع عملياتك المحاسبية الأساسية.</p>
            <ul class="mt-6 text-sm space-y-2 opacity-90">
                <li class="flex items-center justify-center"><i class="fas fa-check-circle ml-2 text-secondary"></i> قيود يومية، صرف، وقبض.</li>
                <li class="flex items-center justify-center"><i class="fas fa-check-circle ml-2 text-secondary"></i> تقارير مالية جاهزة (ميزانية عمومية، ميزان مراجعة).</li>
            </ul>
        </div>
    </div>
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 p-6 sm:p-12">
        <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-10">
            <!-- الشعار -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-gradient-to-br from-secondary to-primary rounded-full flex items-center justify-center">
                    <i class="fas fa-lock-open text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-primary">إعادة تعيين كلمة المرور</h2>
                <p class="mt-2 text-sm text-gray-600">اختر كلمة مرور جديدة لحسابك</p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 ml-2"></i>
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 ml-2"></i>
                        <div>
                            @foreach($errors->all() as $error)
                            <p class="text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input id="email" name="email" type="email" required
                            value="{{ old('email') }}"
                            class="relative block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-200"
                            placeholder="أدخل بريدك الإلكتروني">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            كلمة المرور الجديدة <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition pr-10"
                                placeholder="كلمة المرور الجديدة (6 أحرف على الأقل)">
                            <button type="button" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            تأكيد كلمة المرور <span class="text-red-500">*</span>
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
                            <strong>ملاحظة:</strong> يجب أن تكون كلمة المرور الجديدة مكونة من 6 أحرف على الأقل.
                        </p>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-secondary to-primary hover:from-primary hover:to-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary transition duration-200 shadow-lg">
                        <span class="absolute left-0 inset-y-0 flex items-center pr-3">
                            <i class="fas fa-save"></i>
                        </span>
                        تعيين كلمة المرور الجديدة
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('login') }}" class="font-medium text-secondary hover:text-primary transition duration-200">
                            العودة لتسجيل الدخول
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

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
    </script>
</body>

</html>