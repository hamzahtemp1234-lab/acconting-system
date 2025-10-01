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

    <!-- القسم الأيمن (نموذج تسجيل الدخول) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 p-6 sm:p-12">
        <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-2xl shadow-2xl">

            <div class="text-center mb-10">
                <div class="text-4xl font-extrabold text-primary mb-2">تسجيل الدخول</div>
                <p class="text-gray-500">أدخل بيانات الاعتماد الخاصة بك للوصول إلى لوحة التحكم.</p>
            </div>

            <!-- رسائل الأخطاء العامة -->
            @if (session('error'))
            <div class="mb-4 text-red-600 bg-red-100 border border-red-300 rounded-lg p-3 text-sm text-center">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">اسم المستخدم أو البريد الإلكتروني</label>
                    <div class="relative">
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition duration-150" placeholder="أدخل اسم المستخدم">
                        <i class="fas fa-user absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('username')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition duration-150" placeholder="******">
                        <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-secondary focus:ring-secondary border-gray-300 rounded">
                        <label for="remember" class="mr-2 block text-sm text-gray-700">
                            تذكرني
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-secondary hover:text-primary transition duration-200">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-bold text-primary bg-secondary hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary/50 transition duration-150">
                        تسجيل الدخول <i class="fas fa-sign-in-alt mr-2"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-xs text-gray-500">
                <p>&copy; 2025 نظام المحاسبة المتكامل. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </div>
</body>

</html>