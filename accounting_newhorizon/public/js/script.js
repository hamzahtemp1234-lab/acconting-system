// تطبيق إدارة النظام المحاسبي
class AccountingSystem {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeCharts();
    }

    setupEventListeners() {
        // إدارة القوائم المنسدلة
        this.setupUserMenu();
        
        // إدارة الشريط الجانبي
        this.setupSidebar();
        
        // إدارة النماذج
        this.setupForms();
    }

    setupUserMenu() {
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
            });

            // إغلاق القائمة عند النقر خارجها
            document.addEventListener('click', () => {
                userMenu.classList.add('hidden');
            });
        }
    }

    setupSidebar() {
      const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
    });
        
    }

    setupForms() {
        // التحقق من صحة النماذج
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showError(input, 'هذا الحقل مطلوب');
                isValid = false;
            } else {
                this.clearError(input);
            }
        });

        return isValid;
    }

    showError(input, message) {
        this.clearError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-xs mt-1';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
        input.classList.add('border-red-500');
    }

    clearError(input) {
        const errorDiv = input.parentNode.querySelector('.text-red-500');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.classList.remove('border-red-500');
    }

    initializeCharts() {
        // سيتم تهيئة الرسوم البيانية في الصفحات الخاصة بها
    }

    // وظائف مساعدة
    static formatCurrency(amount, currency = 'ر.س') {
        return new Intl.NumberFormat('ar-SA').format(amount) + ' ' + currency;
    }

    static formatDate(date) {
        return new Intl.DateTimeFormat('ar-SA').format(new Date(date));
    }

    static showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } text-white transform transition-transform duration-300 translate-x-full`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation' : 'info'}-circle mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // إظهار الإشعار
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // إخفاء الإشعار بعد 5 ثوان
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
}

// تهيئة التطبيق عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    new AccountingSystem();
});

// جعل الوظائف متاحة globally
window.AccountingSystem = AccountingSystem;