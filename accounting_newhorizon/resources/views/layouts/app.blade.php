@include('layouts.header')
<!-- زر القائمة للشاشات الصغيرة - في الجانب الأيمن -->
<button id="sidebar-toggle">
    <i class="fas fa-bars"></i>
</button>

<!-- طبقة التعتيم للشاشات الصغيرة -->
<div id="overlay"></div>
@include('layouts.sidebar')
<main class="main-content flex-1 p-6 overflow-auto">
    @yield('content')
</main>
@include('layouts.footer')