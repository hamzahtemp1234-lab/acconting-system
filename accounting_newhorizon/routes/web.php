<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\FiscalYearController;
use App\Http\Controllers\FiscalPeriodController;
use App\Http\Controllers\CostCenterTypeController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierCategoryController;

// Routes for guests (تسجيل الدخول)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Routes for authenticated users (لوحة التحكم)
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // المستخدمين
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    // الأدوار
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    // الصلاحيات
    Route::resource('permissions', PermissionController::class);
    Route::post('permissions/{permission}/toggle-status', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');

    // سجلات النظام
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('logs/{log}', [LogController::class, 'show'])->name('logs.show');
    Route::delete('logs', [LogController::class, 'clear'])->name('logs.clear');
    Route::get('logs/export', [LogController::class, 'export'])->name('logs.export');
    Route::get('logs/statistics', [LogController::class, 'statistics'])->name('logs.statistics');

    // سجلات التدقيق
    Route::get('audit-trails', [AuditTrailController::class, 'index'])->name('audit-trails.index');
    Route::get('audit-trails/{auditTrail}', [AuditTrailController::class, 'show'])->name('audit-trails.show');
    Route::delete('/audit-trails', [AuditTrailController::class, 'clear'])->name('audit-trails.clear');
    Route::get('audit-trails/export', [AuditTrailController::class, 'export'])->name('audit-trails.export');
    Route::post('audit-trails/cleanup', [AuditTrailController::class, 'cleanup'])->name('audit-trails.cleanup');
    Route::get('audit-trails/statistics', [AuditTrailController::class, 'statistics'])->name('audit-trails.statistics');
    Route::prefix('system-settings')->name('system-settings.')->group(function () {
        Route::post('/', [SystemSettingController::class, 'store'])->name('store');
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::get('/create', [SystemSettingController::class, 'setup'])->name('setup');
        Route::get('/{id}/edit', [SystemSettingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SystemSettingController::class, 'update'])->name('update');
        Route::get('/check-status', [SystemSettingController::class, 'checkSystemStatus'])->name('check_status');
    });


    Route::prefix('currencies')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('currencies.index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('currencies.create');
        Route::post('/', [CurrencyController::class, 'store'])->name('currencies.store');
        Route::get('/{id}/edit', [CurrencyController::class, 'edit'])->name('currencies.edit');
        Route::put('/{id}', [CurrencyController::class, 'update'])->name('currencies.update');
        Route::delete('/{id}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');
    });
    Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
        Route::get('/{currency}/create', [ExchangeRateController::class, 'create'])->name('create');
        Route::post('/{currency}', [ExchangeRateController::class, 'store'])->name('store');
    });

    Route::resource('account-types', AccountTypeController::class);


    Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
        Route::get('/', [ChartOfAccountController::class, 'index'])->name('index');
        Route::get('/create', [ChartOfAccountController::class, 'create'])->name('create');
        Route::post('/', [ChartOfAccountController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ChartOfAccountController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ChartOfAccountController::class, 'update'])->name('update');
        Route::delete('/{id}', [ChartOfAccountController::class, 'destroy'])->name('destroy');
    });
    Route::get('chart-of-accounts/next-code', [ChartOfAccountController::class, 'getNextCode'])->name('chart-of-accounts.next-code');
    Route::resource('fiscal-years', FiscalYearController::class);
    Route::resource('fiscal-periods', FiscalPeriodController::class);

    // ✅ استعادة وحذف نهائي
    Route::post('fiscal-periods/{id}/restore', [FiscalPeriodController::class, 'restore'])->name('fiscal-periods.restore');
    Route::delete('fiscal-periods/{id}/force-delete', [FiscalPeriodController::class, 'forceDelete'])->name('fiscal-periods.forceDelete');
    Route::resource('cost-center-types', CostCenterTypeController::class);
    // تصدير
    Route::get('chart-of-accounts/export', [ChartOfAccountController::class, 'export'])->name('chart-of-accounts.export');

    // استيراد
    Route::post('chart-of-accounts/import', [ChartOfAccountController::class, 'import'])->name('chart-of-accounts.import');
    // إضافة حساب من الشجرة عبر Ajax
    Route::post('chart-of-accounts/store-tree', [ChartOfAccountController::class, 'storeFromTree'])
        ->name('chart-of-accounts.store-tree');
    Route::resource('cost-centers', \App\Http\Controllers\CostCenterController::class);

    Route::resource('customers', CustomerController::class);
    Route::resource('customer-categories', App\Http\Controllers\CustomerCategoryController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);


    Route::resource('account-settings', AccountSettingController::class);
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
    });
    Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::get('/chart-of-accounts/tree', [ChartOfAccountController::class, 'tree'])
        ->name('chart-of-accounts.tree'); // ← للتحميل الجزئي بالـ Ajax


    // صفحة الإعدادات الموحّدة (تعرض الوكلاء + الموظفين)
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');

    // حفظ إعدادات الوكلاء
    Route::put('/settings/agents', [SettingsController::class, 'update'])
        ->name('agents-settings.update');

    // حفظ إعدادات الموظفين
    Route::put('/settings/employees', [SettingsController::class, 'updateEmployee'])
        ->name('employee-settings.update');


    // بنوك
    Route::put('/settings/banks', [SettingsController::class, 'updateBanks'])
        ->name('banks-settings.update');

    // صناديق
    Route::put('/settings/cashboxes', [SettingsController::class, 'updateCashboxes'])
        ->name('cashboxes-settings.update');

    Route::resource('agents', AgentController::class);
    // بنوك
    Route::resource('banks', \App\Http\Controllers\BankController::class);

    // صناديق
    Route::resource('cash-boxes', \App\Http\Controllers\CashBoxController::class)->parameters(['cash-boxes' => 'cashbox']);

    // أنواع الوثائق
    Route::resource('document-types', \App\Http\Controllers\DocumentTypeController::class)->parameters([
        'document-types' => 'document_type'
    ]);

    // تسلسل الوثائق
    Route::resource('document-sequences', \App\Http\Controllers\DocumentSequenceController::class)->parameters([
        'document-sequences' => 'document_sequence'
    ]);

    // routes/web.php
    Route::get('/departments/next-code', function (\Illuminate\Http\Request $request) {
        $branchId = (int) $request->query('branch_id');
        abort_unless($branchId > 0, 400, 'branch_id required');
        $code = \App\Models\Department::nextCode($branchId);
        return response()->json(['code' => $code]);
    })->name('departments.next-code')->middleware(['web', 'auth']); // أو بدونه auth حسب إعدادك

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

    Route::resource('supplier-categories', SupplierCategoryController::class);
});
// routes للبروفايل
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::post('/remove-avatar', [ProfileController::class, 'removeAvatar'])->name('remove-avatar'); // ← إضافة جديدة للبروفايل
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password.update');
    Route::get('/activity-log', [ProfileController::class, 'activityLog'])->name('activity-log');
});

// // Redirect root
// Route::get('/', function () {
//     return auth()->check() ? redirect('/dashboard') : redirect('/login');
// });
