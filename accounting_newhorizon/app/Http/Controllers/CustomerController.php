<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Currency;
use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // بحث بسيط اختياري
        $q = $request->get('q');

        $customers = Customer::with(['currency', 'account', 'category'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('code', 'like', "%$q%")
                        ->orWhere('name', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->orderBy('code')
            ->paginate(15);

        // إحصائيات للبطاقات
        $stats = [
            'total'        => Customer::count(),
            'active'       => Customer::where('is_active', true)->count(),
            'companies'    => Customer::where('type', 'company')->count(),
            'individuals'  => Customer::where('type', 'individual')->count(),
        ];

        return view('customers.index', compact('customers', 'stats', 'q'));
    }

    public function create()
    {
        $currencies = Currency::orderBy('code')->get();
        $accounts   = ChartOfAccount::orderBy('code')->get();
        $categories = CustomerCategory::orderBy('name')->get();
        $nextCode   = \App\Models\Customer::nextCode(); // للعرض فقط

        return view('customers.create', compact('currencies', 'accounts', 'categories', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name'   => 'required|string|max:255',
                'type'   => ['required', Rule::in(['individual', 'company'])],
                'tax_id' => 'nullable|string|max:100',
                'id_number' => 'nullable|string|max:100',
                'phone'  => 'nullable|string|max:20',
                'mobile' => 'nullable|string|max:20',
                'email'  => 'nullable|email|max:255',
                'address' => 'nullable|string|max:255',
                'city'   => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',

                'currency_id' => 'nullable|exists:currencies,id',
                // لا نطلب account_id هنا
                'category_id' => 'nullable|exists:customer_categories,id',

                'credit_limit' => 'nullable|numeric',
                'opening_balance' => 'nullable|numeric',
                'opening_balance_date' => 'nullable|date',

                'payment_terms' => 'nullable|string|max:255',
                'preferred_payment_method' => ['nullable', Rule::in(['cash', 'bank', 'cheque', 'card'])],

                'is_active' => 'required|boolean',
                'notes' => 'nullable|string',
            ],
            [
                'name.required' => 'حقل الاسم مطلوب',
                'type.in'       => 'نوع العميل غير صحيح',
                'email.email'   => 'البريد الإلكتروني غير صالح',
            ]
        );

        DB::transaction(function () use (&$validated) {
            // إذا التصنيف مربوط بحساب مجموعة
            if (!empty($validated['category_id'])) {
                $category = CustomerCategory::select('id', 'account_id')->find($validated['category_id']);

                if ($category && $category->account_id) {
                    $parent = ChartOfAccount::findOrFail($category->account_id);

                    // اتباع نفس مفهومك: الأب يكون مجموعة
                    if (!$parent->is_group) {
                        abort(422, 'حساب التصنيف يجب أن يكون "مجموعة" (is_group = true).');
                    }

                    // توليد الكود والمستوى بنفس منطق ChartOfAccountController
                    $childCode  = $this->generateNextCode($parent->id);
                    $childLevel = $this->calculateLevel($parent->id);

                    // الطبيعة والنوع والعملة والحالة من الأب (كما تفعل عادةً)
                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $childCode,
                        'name'            => $validated['name'], // اسم العميل
                        'description'     => 'حساب عميل: ' . $validated['name'],
                        'account_type_id' => $parent->account_type_id,
                        'nature'          => $parent->nature,      // debit/credit
                        'is_group'        => false,                // حساب حركة
                        'level'           => $childLevel,
                        'currency_id'     => $parent->currency_id,
                        'allow_entry'     => true,                 // يسمح بالقيد
                        'is_default'      => false,
                        'status'          => $parent->status ?? 'نشط', // اتبع حالة الأب
                    ]);

                    // اربط الحساب الوليد بالعميل
                    $validated['account_id'] = $child->id;
                }
            }

            $customer = Customer::create($validated);
            //add  تسجيل في سجل التدقيق
            AuditTrailController::log(
                'customers',
                $customer->id,
                'I',
                'أضافة عميل جديد: ' . $customer->name,
                Auth::id()
            );
        });

        return redirect()->route('customers.index')->with('success', 'تم إضافة العميل بنجاح');
    }

    public function edit(Customer $customer)
    {
        $currencies = Currency::orderBy('code')->get();
        $accounts   = ChartOfAccount::orderBy('code')->get();
        $categories = CustomerCategory::orderBy('name')->get();

        return view('customers.edit', compact('customer', 'currencies', 'accounts', 'categories'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate(
            [
                'name'   => 'required|string|max:255',
                'type'   => ['required', Rule::in(['individual', 'company'])],
                'tax_id' => 'nullable|string|max:100',
                'id_number' => 'nullable|string|max:100',
                'phone'  => 'nullable|string|max:20',
                'mobile' => 'nullable|string|max:20',
                'email'  => 'nullable|email|max:255',
                'address' => 'nullable|string|max:255',
                'city'   => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',

                'currency_id' => 'nullable|exists:currencies,id',
                'category_id' => 'nullable|exists:customer_categories,id',

                'credit_limit' => 'nullable|numeric',
                'opening_balance' => 'nullable|numeric',
                'opening_balance_date' => 'nullable|date',

                'payment_terms' => 'nullable|string|max:255',
                'preferred_payment_method' => ['nullable', Rule::in(['cash', 'bank', 'cheque', 'card'])],

                'is_active' => 'required|boolean',
                'notes' => 'nullable|string',
            ]
        );

        DB::transaction(function () use (&$validated, $customer) {

            $oldCategoryId = $customer->category_id;
            $newCategoryId = $validated['category_id'] ?? null;

            // 1) تحديث/إنشاء/نقل الحساب المرتبط
            // إذا لدى العميل حساب مرتبط
            if ($customer->account_id) {
                $child = ChartOfAccount::find($customer->account_id);

                if ($child) {
                    // تحدّث اسم الحساب إلى اسم العميل
                    $child->name        = $validated['name'];
                    $child->description = 'حساب عميل: ' . $validated['name'];

                    // هل تغيّر التصنيف إلى أبٍ جديد؟
                    if ($newCategoryId && $newCategoryId !== $oldCategoryId) {
                        $newCategory = CustomerCategory::select('id', 'account_id')->find($newCategoryId);
                        if ($newCategory && $newCategory->account_id) {
                            $newParent = ChartOfAccount::findOrFail($newCategory->account_id);

                            if (!$newParent->is_group) {
                                abort(422, 'حساب التصنيف الجديد يجب أن يكون "مجموعة".');
                            }

                            // توليد كود جديد ومستوى جديد بنفس طريقتك
                            $newCode  = $this->generateNextCode($newParent->id);
                            $newLevel = $this->calculateLevel($newParent->id);

                            // نقل وإرث خصائص الأب الجديد
                            $child->parent_id       = $newParent->id;
                            $child->code            = $newCode;
                            $child->level           = $newLevel;
                            $child->account_type_id = $newParent->account_type_id;
                            $child->nature          = $newParent->nature;
                            $child->currency_id     = $newParent->currency_id;
                            $child->status          = $newParent->status ?? $child->status;

                            // تأكد أنه Leaf
                            $child->is_group    = false;
                            $child->allow_entry = true;
                        }
                        // إن لم يكن للتصنيف الجديد حساب أب، لا ننقل؛ فقط نُحدّث الاسم.
                    }

                    $child->save();
                }
            } else {
                // لا يملك حسابًا سابقًا → أنشئ له حسابًا تحت أبّ التصنيف إن وجد
                if ($newCategoryId) {
                    $newCategory = CustomerCategory::select('id', 'account_id')->find($newCategoryId);
                    if ($newCategory && $newCategory->account_id) {
                        $parent = ChartOfAccount::findOrFail($newCategory->account_id);
                        if (!$parent->is_group) {
                            abort(422, 'حساب التصنيف يجب أن يكون "مجموعة".');
                        }

                        $code  = $this->generateNextCode($parent->id);
                        $level = $this->calculateLevel($parent->id);

                        $child = ChartOfAccount::create([
                            'parent_id'       => $parent->id,
                            'code'            => $code,
                            'name'            => $validated['name'],
                            'description'     => 'حساب عميل: ' . $validated['name'],
                            'account_type_id' => $parent->account_type_id,
                            'nature'          => $parent->nature,
                            'is_group'        => false,
                            'level'           => $level,
                            'currency_id'     => $parent->currency_id,
                            'allow_entry'     => true,
                            'is_default'      => false,
                            'status'          => $parent->status ?? 'نشط',
                        ]);

                        $validated['account_id'] = $child->id;
                    }
                }
            }

            // 2) تحديث سجل العميل نفسه
            $customer->update($validated);
            //edit تسجيل في سجل التدقيق
            AuditTrailController::log(
                'customers',
                $customer->id,
                'U',
                'تعديل بيانات العميل: ' . $customer->name,
                Auth::id()
            );
        });

        return redirect()->route('customers.index')->with('success', 'تم تحديث بيانات العميل والحساب المرتبط');
    }
    /**
     * توليد الكود القادم تمامًا كما في ChartOfAccountController
     */
    private function generateNextCode($parentId = null)
    {
        if (!$parentId) {
            // المستوى الأول (حساب رئيسي)
            $lastMain = ChartOfAccount::whereNull('parent_id')->orderBy('code', 'desc')->first();
            return $lastMain ? $lastMain->code + 1 : 1;
        }

        $parent = ChartOfAccount::findOrFail($parentId);
        $level = $parent->level + 1;

        // آخر ابن تحت هذا الأب
        $lastChild = ChartOfAccount::where('parent_id', $parentId)->orderBy('code', 'desc')->first();

        if (!$lastChild) {
            // أول ابن بحسب المستوى
            if ($level == 2) {
                return $parent->code . '1';    // مثال: 1 → 11
            } elseif ($level == 3) {
                return $parent->code . '01';   // 11 → 1101
            } elseif ($level == 4) {
                return $parent->code . '001';  // 1101 → 1101001
            } elseif ($level == 5) {
                return $parent->code . '0001'; // …
            } elseif ($level == 6) {
                return $parent->code . '00001';
            }
        } else {
            return $lastChild->code + 1; // 11 → 12
        }

        return null;
    }

    /**
     * حساب المستوى (level) بنفس منطقك
     */
    private function calculateLevel($parentId)
    {
        if (!$parentId) {
            return 1;
        }

        $parent = ChartOfAccount::find($parentId);
        return $parent ? $parent->level + 1 : 1;
    }


    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {

            $accountId = $customer->account_id;

            // 1) احذف العميل (Soft Delete)
            $customer->delete();

            // 2) تعامل مع الحساب المرتبط إن وجد
            if ($accountId) {
                $account = ChartOfAccount::find($accountId);
                if ($account) {

                    // لو فيه أبناء (المفروض لا، لأنه حساب عميل Leaf)
                    if ($account->children()->exists()) {
                        // اجعله غير نشط بدل الحذف
                        $account->status = 'غير نشط';
                        $account->save();
                        return;
                    }

                    // 🔎 (اختياري) افحص إن كان عليه حركات يومية
                    // عدّل اسم جدول/علاقة القيود حسب نظامك (مثال journal_entries_lines)
                    $hasMovements = false;
                    // مثال إن كانت عندك علاقة معرفة في الموديل:
                    // $hasMovements = $account->journalLines()->exists();

                    if ($hasMovements) {
                        // لا نحذفه - فقط نعطّله
                        $account->status = 'غير نشط';
                        $account->save();
                    } else {
                        // حذف ناعم للحساب
                        $account->delete();
                    }
                }
            }
            //delete تسجيل في سجل التدقيق
            AuditTrailController::log(
                'customers',
                $customer->id,
                'D',
                'حذف بيانات العميل: ' . $customer->name,
                Auth::id()
            );
        });

        return redirect()->route('customers.index')->with('success', 'تم حذف العميل ومعالجة الحساب المرتبط');
    }

    public function restore($id)
    {
        $customer = \App\Models\Customer::withTrashed()->findOrFail($id);

        DB::transaction(function () use ($customer) {
            $customer->restore();

            if ($customer->account_id) {
                $account = ChartOfAccount::withTrashed()->find($customer->account_id);
                if ($account && $account->trashed()) {
                    $account->restore();
                    // ممكن ترجّعه نشطًا
                    $account->status = 'نشط';
                    $account->save();
                }
            }
        });
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'customers',
            $customer->id,
            'U',
            'أستعادة بيانات العميل: ' . $customer->name,
            Auth::id()
        );
        return redirect()->route('customers.index')->with('success', 'تم استرجاع العميل وحسابه');
    }
}
