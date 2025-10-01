 <script>
     // تهيئة الرسم البياني
     document.addEventListener('DOMContentLoaded', function() {


         // التحكم في القائمة الجانبية للشاشات الصغيرة
         const sidebarToggle = document.getElementById('sidebar-toggle');
         const closeSidebar = document.getElementById('close-sidebar');
         const sidebar = document.getElementById('sidebar');
         const overlay = document.getElementById('overlay');

         // إظهار زر القائمة على الشاشات الصغيرة فقط
         function checkScreenSize() {
             if (window.innerWidth < 769) {
                 sidebarToggle.style.display = 'block';
                 sidebar.classList.remove('active');
                 overlay.classList.remove('active');
             } else {
                 sidebarToggle.style.display = 'none';
                 sidebar.classList.add('active');
                 overlay.classList.remove('active');
             }
         }

         // التحقق من حجم الشاشة عند التحميل وعند تغيير الحجم
         checkScreenSize();
         window.addEventListener('resize', checkScreenSize);

         if (sidebarToggle) {
             sidebarToggle.addEventListener('click', function() {
                 sidebar.classList.add('active');
                 overlay.classList.add('active');
             });
         }

         if (closeSidebar) {
             closeSidebar.addEventListener('click', function() {
                 sidebar.classList.remove('active');
                 overlay.classList.remove('active');
             });
         }

         overlay.addEventListener('click', function() {
             sidebar.classList.remove('active');
             overlay.classList.remove('active');
         });

         // إغلاق القائمة عند النقر على رابط في القائمة (للشاشات الصغيرة)
         const sidebarLinks = document.querySelectorAll('#sidebar a');
         sidebarLinks.forEach(link => {
             link.addEventListener('click', function() {
                 if (window.innerWidth < 769) {
                     sidebar.classList.remove('active');
                     overlay.classList.remove('active');
                 }
             });
         });
     });
 </script>
 </body>

 </html>