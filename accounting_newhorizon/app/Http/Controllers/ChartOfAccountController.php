<?php

namespace App\Http\Controllers;

use App\Exports\ChartOfAccountsExport;
use App\Imports\ChartOfAccountsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerCategory;
use Illuminate\Support\Facades\DB;
use App\Models\AccountType;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    /**
     * عرض جميع الحسابات (قائمة + شجرة)
     */
    public function index(Request $request)
    {
        $viewMode = $request->input('view', 'table');

        // ===== 1) قراءة الفلاتر =====
        $filters = [
            'q'               => trim((string)$request->input('q')),
            'status'          => $request->input('status'),                // 'نشط' | 'غير نشط' | null
            'nature'          => $request->input('nature'),                // 'debit' | 'credit' | null
            'is_group'        => $request->input('is_group'),              // '1' | '0' | null
            'account_type_id' => $request->input('account_type_id'),       // id | null
            'currency_id'     => $request->input('currency_id'),           // id | null
        ];

        // ===== 2) أساس الاستعلام للفلاتر (للاستخدام في الجدول + الإحصائيات) =====
        $baseQuery = ChartOfAccount::query();

        // تطبيق الفلاتر
        $this->applyFilters($baseQuery, $filters);

        // ===== 3) الجدول: Paginate مع الفلاتر =====
        $accountsTable = (clone $baseQuery)
            ->with(['parent', 'accountType', 'currency'])
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        // ===== 4) الشجرة: نحمّل كل الجذور + أولادهم (بدون ترقيم) =====
        // ملاحظة: الشجرة في السيرفر تُعرض كاملة؛ سنطبّق الفلاتر بالـ JS لإخفاء/إظهار.
        $treeRoots = ChartOfAccount::with(['childrenRecursive', 'accountType', 'currency'])
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        // ===== 5) الإحصائيات (حسب الفلاتر الحالية) =====
        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'groups'    => (clone $baseQuery)->where('is_group', true)->count(),
            'leaves'    => (clone $baseQuery)->where('is_group', false)->count(),
            'active'    => (clone $baseQuery)->where('status', 'نشط')->count(),
            'inactive'  => (clone $baseQuery)->where('status', 'غير نشط')->count(),
            'debit'     => (clone $baseQuery)->where('nature', 'debit')->count(),
            'credit'    => (clone $baseQuery)->where('nature', 'credit')->count(),
        ];

        $accountTypes = AccountType::orderBy('name')->get();
        $currencies   = Currency::orderBy('code')->get();

        // لائحة آباء (مجموعة) للمودال
        $allGroupParents = ChartOfAccount::where('is_group', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return view(
            'chart-of-accounts.index',
            compact('viewMode', 'accountsTable', 'treeRoots', 'accountTypes', 'currencies', 'allGroupParents', 'filters', 'stats')
        );
    }

    private function applyFilters($query, array $filters): void
    {
        if ($filters['q']) {
            $q = $filters['q'];
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        if ($filters['status'] !== null && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }
        if ($filters['nature'] !== null && $filters['nature'] !== '') {
            $query->where('nature', $filters['nature']);
        }
        if ($filters['is_group'] !== null && $filters['is_group'] !== '') {
            $query->where('is_group', (bool)$filters['is_group']);
        }
        if ($filters['account_type_id']) {
            $query->where('account_type_id', $filters['account_type_id']);
        }
        if ($filters['currency_id']) {
            $query->where('currency_id', $filters['currency_id']);
        }
    }


    // إضافة ابن من المودال عبر Ajax (تأكد أن الراوت موجود)
    public function storeFromTree(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:20|unique:chart_of_accounts,code',
            'name'            => 'required|string|max:255',
            'nature'          => 'required|in:debit,credit',
            'account_type_id' => 'nullable|exists:account_types,id',
            'currency_id'     => 'nullable|exists:currencies,id',
            'status'          => 'required|in:نشط,غير نشط',
            'parent_id'       => 'nullable|exists:chart_of_accounts,id',
            'is_group'        => 'nullable|boolean',
            'description'     => 'nullable|string',
        ]);

        $parentId = $request->input('parent_id');
        $level    = $this->calculateLevel($parentId);

        $account = ChartOfAccount::create([
            'parent_id'       => $parentId,
            'code'            => $validated['code'],
            'name'            => $validated['name'],
            'description'     => $validated['description'] ?? null,
            'account_type_id' => $validated['account_type_id'] ?? null,
            'nature'          => $validated['nature'],
            'is_group'        => $request->boolean('is_group'),
            'level'           => $level,
            'currency_id'     => $validated['currency_id'] ?? null,
            'allow_entry'     => !$request->boolean('is_group'),
            'is_default'      => false,
            'status'          => $validated['status'],
        ]);
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'chart_of_accounts',
            $account->id,
            'I',
            'أضافة حساب جديد : ' . $account->name,
            Auth::id()
        );
        return response()->json([
            'success' => true,
            'account' => $account->load('parent'),
        ]);
    }

    /**
     * نموذج إنشاء حساب جديد
     */
    public function create()
    {
        $parentAccounts = ChartOfAccount::where('is_group', true)->get(); // 👈 الأب لازم يكون مجموعة
        $accountTypes = AccountType::all();
        $currencies = Currency::all();

        $lastAccount = \App\Models\ChartOfAccount::orderBy('id', 'desc')->first();
        // الكود الجديد للحساب الرئيسي (في البداية)
        $nextCode = $this->generateNextCode();
        $parents = \App\Models\ChartOfAccount::all();

        return view('chart-of-accounts.create', compact('parentAccounts', 'accountTypes', 'currencies', 'nextCode'));
    }

    /**
     * حفظ حساب جديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|max:20|unique:chart_of_accounts,code',
            'name'            => 'required|string|max:255',
            'account_type_id' => 'nullable|exists:account_types,id',
            'nature'          => 'required|string|in:debit,credit',
            'currency_id'     => 'nullable|exists:currencies,id',
            'status'          => 'required|string|in:نشط,غير نشط',
        ]);

        // إذا تم اختيار نوع حساب → أخذ الطبيعة منه
        $nature = $request->nature;
        if ($request->account_type_id) {
            $type   = AccountType::find($request->account_type_id);
            $nature = $type ? $type->nature : $nature;
        }

        $char = ChartOfAccount::create([
            'parent_id'       => $request->parent_id,
            'code'            => $request->code,
            'name'            => $request->name,
            'description'     => $request->description,
            'account_type_id' => $request->account_type_id,
            'nature'          => $nature,
            'is_group'        => $request->has('is_group'),
            'level'           => $this->calculateLevel($request->parent_id),
            'currency_id'     => $request->currency_id,
            'allow_entry'     => !$request->has('is_group'),
            'is_default'      => $request->is_default ?? false,
            'status'          => $request->status,
        ]);
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'chart_of_accounts',
            $char->id,
            'I',
            'أضافة حساب جديد : ' . $char->name,
            Auth::id()
        );
        return redirect()->route('chart-of-accounts.index', ['view' => $request->input('view', 'table')])
            ->with('success', 'تم إضافة الحساب بنجاح');
    }
    /**
     * إضافة حساب من الشجرة عبر Ajax فقط
     */


    /**
     * فورم تعديل حساب.
     */
    public function edit($id)
    {
        $account      = ChartOfAccount::findOrFail($id);
        $accountTypes = AccountType::all();
        $currencies   = Currency::all();
        $parentAccounts = ChartOfAccount::where('is_group', true)->where('id', '!=', $id)->get();

        return view('chart-of-accounts.edit', compact('account', 'accountTypes', 'currencies', 'parentAccounts'));
    }

    /**
     * تحديث بيانات الحساب.
     */
    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $request->validate([
            'code'            => 'required|string|max:20|unique:chart_of_accounts,code,' . $account->id,
            'name'            => 'required|string|max:255',
            'account_type_id' => 'nullable|exists:account_types,id',
            'nature'          => 'required|string|in:debit,credit',
            'currency_id'     => 'nullable|exists:currencies,id',
            'status'          => 'required|string|in:نشط,غير نشط',
        ]);

        // الطبيعة من نوع الحساب (إن وُجد)
        $nature = $request->nature;
        if ($request->account_type_id) {
            $type   = AccountType::find($request->account_type_id);
            $nature = $type ? $type->nature : $nature;
        }

        // احتفظ بالأب القديم لكي نعرف إن تغيّر
        $oldParentId = $account->parent_id;

        $account->fill([
            'parent_id'       => $request->parent_id,
            'code'            => $request->code,
            'name'            => $request->name,
            'description'     => $request->description,
            'account_type_id' => $request->account_type_id,
            'nature'          => $nature,
            'is_group'        => $request->has('is_group'),
            'level'           => $this->calculateLevel($request->parent_id),
            'currency_id'     => $request->currency_id,
            'allow_entry'     => !$request->has('is_group'),
            'is_default'      => $request->is_default ?? false,
            'status'          => $request->status,
        ]);

        if (!$account->isDirty()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['no_change' => '⚠️ لم يتم تعديل أي بيانات']);
        }

        DB::transaction(function () use ($account, $oldParentId, $request) {
            $account->save();

            // 🔄 مزامنة العميل المرتبط (إن وُجد وكان الحساب Leaf يسمح بالقيد)
            $this->syncLinkedCustomerAfterAccountUpdate($account, $oldParentId);
        });

        // سجل التدقيق (كما عندك)
        AuditTrailController::log(
            'chart_of_accounts',
            $account->id,
            'U',
            'تعديل الحساب : ' . $account->name,
            Auth::id()
        );

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'تم تحديث الحساب ومزامنة العميل المرتبط');
    }


    /**
     * حذف حساب (Soft Delete).
     */
    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);

        // لا نحذف إذا لديه أبناء
        if ($account->children()->exists()) {
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'لا يمكن حذف حساب يحتوي على حسابات فرعية');
        }

        DB::transaction(function () use ($account) {

            // إن كان مرتبطًا بعميل → احذف العميل أولًا (Soft Delete)
            $customer = Customer::where('account_id', $account->id)->first();
            if ($customer) {
                $customer->delete(); // Soft delete للعميل
            }

            // ثم احذف الحساب (Soft Delete)
            $account->delete();
        });

        AuditTrailController::log(
            'chart_of_accounts',
            $account->id,
            'D',
            'حذف الحساب : ' . $account->name,
            Auth::id()
        );

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'تم حذف الحساب والعميل المرتبط (إن وُجد)');
    }



    private function generateNextCode($parentId = null)
    {

        if (!$parentId) {
            // المستوى الأول (حساب رئيسي)
            $lastMain = ChartOfAccount::whereNull('parent_id')->orderBy('code', 'desc')->first();
            return $lastMain ? $lastMain->code + 1 : 1;
        }

        $parent = ChartOfAccount::findOrFail($parentId);
        $level = $parent->level + 1;

        // جلب آخر ابن
        $lastChild = ChartOfAccount::where('parent_id', $parentId)->orderBy('code', 'desc')->first();

        if (!$lastChild) {
            // أول ابن
            if ($level == 2) {
                return $parent->code . '1'; // مثال: 1 → 11
            } elseif ($level == 3) {
                return $parent->code . '01'; // مثال: 11 → 1101
            } elseif ($level == 4) {
                return $parent->code . '001'; // مثال: 1101 → 1101001
            } elseif ($level == 5) {
                return $parent->code . '0001'; // مثال: 1101 → 1101001
            } elseif ($level == 6) {
                return $parent->code . '00001'; // مثال: 1101 → 1101001
            }
        } else {

            return $lastChild->code + 1; // 11 → 12

        }

        return null;
    }


    /**
     * حساب المستوى (Level) حسب الحساب الأب.
     */
    private function calculateLevel($parentId)
    {
        if (!$parentId) {
            return 1;
        }

        $parent = ChartOfAccount::find($parentId);
        return $parent ? $parent->level + 1 : 1;
    }
    public function getNextCode(Request $request)
    {
        $parentId = $request->get('parent_id');
        $nextCode = $this->generateNextCode($parentId);

        return response()->json(['nextCode' => $nextCode]);
    }
    // ✅ تصدير
    public function export()
    {

        return Excel::download(new ChartOfAccountsExport, 'chart_of_accounts.xlsx');
    }

    // ✅ استيراد
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new ChartOfAccountsImport, $request->file('file'));

        return redirect()->route('chart-of-accounts.index')->with('success', 'تم استيراد الحسابات بنجاح');
    }
    /**
     * مزامنة بيانات العميل المرتبط عند تعديل الحساب:
     * - تحديث اسم العميل من اسم الحساب (إن اختلف)
     * - إن تغيّر الأب → تحديث category_id للعميل حسب تصنيف يطابق account_id للأب الجديد
     * - تحديث is_active للعميل بناء على status للحساب
     */
    private function syncLinkedCustomerAfterAccountUpdate(ChartOfAccount $account, $oldParentId = null): void
    {
        // فقط للحسابات التي تسمح بالقيد (حساب عميل Leaf)
        if (!$account->allow_entry) {
            return;
        }

        $customer = Customer::where('account_id', $account->id)->first();
        if (!$customer) {
            return;
        }

        $changed = false;

        // 1) مزامنة الاسم
        if ($account->name && $customer->name !== $account->name) {
            $customer->name = $account->name;
            $changed = true;
        }

        // 2) إن تغيّر الأب → حاول تعيين تصنيف العميل حسب الأب الجديد
        if (!is_null($oldParentId) && $oldParentId != $account->parent_id) {
            if ($account->parent_id) {
                // نبحث عن تصنيف عميل مرتبط بهذا الأب
                $newCategory = CustomerCategory::where('account_id', $account->parent_id)->first();
                if ($newCategory) {
                    $customer->category_id = $newCategory->id;
                    $changed = true;
                } else {
                    // إن لم نجد تصنيفًا لهذا الأب، يمكنك إما ترك التصنيف كما هو، أو تفريغه:
                    // $customer->category_id = null; $changed = true;
                }
            } else {
                // لو صار بدون أب (جذر) — غالبًا لا يحدث لحسابات العملاء
                // $customer->category_id = null; $changed = true;
            }
        }

        // 3) مزامنة الحالة حسب حالة الحساب
        $isActive = ($account->status === 'نشط');
        if ($customer->is_active != $isActive) {
            $customer->is_active = $isActive;
            $changed = true;
        }

        if ($changed) {
            $customer->save();
        }
    }
    // app/Http/Controllers/ChartOfAccountController.php

    public function tree(Request $request)
    {
        // 20 جذر بالصفحة
        $rootAccounts = \App\Models\ChartOfAccount::with(['childrenRecursive', 'accountType', 'currency'])
            ->whereNull('parent_id')
            ->orderBy('code')
            ->paginate(20);

        // نرجّع Partial Blade
        // إن كان الطلب Ajax: نرجع html فقط داخل JSON
        if ($request->ajax()) {
            $html = view('chart-of-accounts._tree_list', compact('rootAccounts'))->render();
            return response()->json(['html' => $html]);
        }

        // كحالة احتياطية لو تم الوصول مباشرةً
        return view('chart-of-accounts._tree_list', compact('rootAccounts'));
    }
}
