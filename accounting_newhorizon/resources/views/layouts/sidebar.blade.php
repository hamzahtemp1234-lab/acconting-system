 <aside id="sidebar" class="bg-primary text-white p-4 flex flex-col transition-all duration-300">
     <div class="flex justify-between items-center mb-8 text-secondary border-b border-secondary/50 pb-4">
         <div class="text-2xl font-bold">نظام المحاسبة</div>
         <button id="close-sidebar" class="text-white text-xl">
             <i class="fas fa-times"></i>
         </button>
     </div>
     <nav class="space-y-2 flex-grow">
         <a href="#" class="flex items-center p-3 rounded-lg bg-secondary/20 font-bold hover:bg-secondary/40 transition">
             <i class="fas fa-tachometer-alt ml-3"></i> لوحة التحكم
         </a>


         <!-- العمليات -->
         <div class="pt-4 border-t border-white/20">
             <p class="text-xs text-white/50 uppercase mb-2">العمليات</p>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">سندات القبض</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">سندات الصرف</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">قيود اليومية</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">الفواتير</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">المشتريات والمبيعات</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">إدارة المخزون</a>
         </div>

         <!-- التقارير المالية -->
         <div class="pt-4 border-t border-white/20">
             <p class="text-xs text-white/50 uppercase mb-2">التقارير المالية</p>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">ميزانية المراجعة</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">الأستاذ العام</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">الميزانية العمومية</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">كشف حساب العملاء</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">كشف حساب الموردين</a>
             <a href="#" class="block p-2 rounded-lg hover:bg-white/10 transition">تقرير الأرباح والخسائر</a>
         </div>

         <!-- التهيئات -->
         <div class="pt-4 border-t border-white/20">
             <p class="text-xs text-white/50 uppercase mb-2">التهيئات</p>
             <a href="{{ route('fiscal-years.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">السنوات المالية</a>
             <a href="{{ route('fiscal-periods.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">الفترات المالية</a>
             <a href="{{ route('currencies.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">العملات</a>
             <a href="{{ route('account-types.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">أنواع الحسابات</a>
             <a href="{{ route('chart-of-accounts.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">شجرة الحسابات</a>
             <a href="{{ route('cost-center-types.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">أنواع مراكز التكلفة</a>
             <a href="{{ route('cost-centers.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">مراكز التكلفة</a>
             <a href="{{ route('customer-categories.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">تصنيفات العملاء</a>
             <a href="{{ route('customers.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">إدارة العملاء</a>
             <a href="{{ route('agents.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">إدارة الوكلاء</a>
             <a href="{{ route('cash-boxes.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">إدارة الصناديق</a>
             <a href="{{ route('supplier-categories.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">تصنيفات المودرين</a>
             <a href="#{{-- route('suppliers.index') --}}" class="block p-2 rounded-lg hover:bg-white/10 transition">إدارة الموردين</a>
             <a href="{{ route('settings.edit') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">أعدادات الحسابات</a>
         </div>
         <!-- إدارة النظام والصلاحيات -->
         <div class="pt-4 border-t border-white/20">
             <p class="text-xs text-white/50 uppercase mb-2">إدارة النظام والصلاحيات</p>
             <a href="{{ route('system-settings.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">أعدادات النظام</a>
             <a href="{{ route('branches.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">الفروع</a>
             <a href="{{ route('departments.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">الأقسام</a>

             <a href="{{ route('employees.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">الموظفين</a>
             <a href="{{ route('users.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">المستخدمين</a>
             <a href="{{ route('roles.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">الأدوار</a>
             <a href="{{ route('permissions.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">الأذونات</a>
             <!-- <a href="{{ route('logs.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">سجل النشاطات (Logs)</a> -->
             <a href="{{ route('audit-trails.index') }}" class="block p-2 rounded-lg hover:bg-white/10 transition">مسارات التدقيق (Audit Trails)</a>
         </div>
     </nav>

     <div class="mt-auto border-t border-white/20 pt-4">
         @auth
         <!-- عندما يكون المستخدم مسجل الدخول -->
         <a href="{{ route('profile.show') }}" class="flex items-center p-2 hover:bg-white/10 rounded-lg cursor-pointer transition duration-200">
             @if(auth()->user()->avatar)
             <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="User" class="w-8 h-8 rounded-full ml-2 border border-white/20">
             @else
             <!-- صورة افتراضية -->
             <div class="w-8 h-8 rounded-full bg-gradient-to-br from-secondary to-primary flex items-center justify-center ml-2">
                 <span class="text-white text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
             </div>
             @endif
             <div class="flex-1 text-right">
                 <span class="text-sm block text-white">{{ auth()->user()->name }}</span>
                 <span class="text-xs text-gray-300 block">
                     @foreach(auth()->user()->roles as $role)
                     {{ $role->RoleName }}@if(!$loop->last), @endif
                     @endforeach
                 </span>
             </div>
         </a>

         <!-- نموذج تسجيل الخروج -->
         <form method="POST" action="{{ route('logout') }}" class="mt-2">
             @csrf
             <button type="submit" class="w-full text-center text-xs text-secondary hover:underline transition duration-200">
                 <i class="fas fa-sign-out-alt ml-1"></i> تسجيل الخروج
             </button>
         </form>

         @endauth
     </div>
 </aside>