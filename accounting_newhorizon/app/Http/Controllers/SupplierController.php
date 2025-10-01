<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ChartOfAccount;
use App\Models\SupplierCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // بحث بسيط اختياري
        $q = $request->get('q');

        $suppliers = Supplier::with(['account', 'category'])
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

        // بطاقات إحصائية
        $stats = [
            'total'   => Supplier::count(),
            'active'  => Supplier::where('is_active', true)->count(),
            'with_account' => Supplier::whereNotNull('account_id')->count(),
        ];

        return view('suppliers.index', compact('suppliers', 'stats', 'q'));
    }

    public function create()
    {
        $accounts   = ChartOfAccount::orderBy('code')->get(); // في حال أردت ربط يدوي لاحقًا
        $categories = SupplierCategory::orderBy('name')->get();
        $nextCode   = Supplier::nextCode(); // للعرض فقط

        return view('suppliers.create', compact('accounts', 'categories', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name'     => 'required|string|max:255',
                'phone'    => 'nullable|string|max:50',
                'email'    => 'nullable|email|max:100',
                'category_id' => 'nullable|exists:suplier_categories,id',
                'is_active'   => 'required|boolean',
            ],
            [
                'name.required' => 'حقل الاسم مطلوب',
                'email.email'   => 'البريد الإلكتروني غير صالح',
            ]
        );

        DB::transaction(function () use (&$validated) {
            // إن تم اختيار تصنيف وله حساب أب (مجموعة) → أنشئ حسابًا تحته باسم المورد
            if (!empty($validated['category_id'])) {
                $category = SupplierCategory::select('id', 'account_id')->find($validated['category_id']);

                if ($category && $category->account_id) {
                    $parent = ChartOfAccount::findOrFail($category->account_id);

                    if (!$parent->is_group) {
                        abort(422, 'حساب التصنيف يجب أن يكون "مجموعة" (is_group = true).');
                    }

                    $childCode  = $this->generateNextCode($parent->id);
                    $childLevel = $this->calculateLevel($parent->id);

                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $childCode,
                        'name'            => $validated['name'], // اسم المورد
                        'description'     => 'حساب مورد: ' . $validated['name'],
                        'account_type_id' => $parent->account_type_id,
                        'nature'          => $parent->nature,
                        'is_group'        => false,
                        'level'           => $childLevel,
                        'currency_id'     => $parent->currency_id,
                        'allow_entry'     => true,
                        'is_default'      => false,
                        'status'          => $parent->status ?? 'نشط',
                    ]);

                    $validated['account_id'] = $child->id;
                }
            }

            $supplier = Supplier::create($validated);
            //add  تسجيل في سجل التدقيق
            AuditTrailController::log(
                'suppliers',
                $supplier->id,
                'I',
                'أضافة مورد جديد: ' . $supplier->name,
                Auth::id()
            );
        });

        return redirect()->route('suppliers.index')->with('success', 'تم إضافة المورد بنجاح');
    }

    public function edit(Supplier $supplier)
    {
        $accounts   = ChartOfAccount::orderBy('code')->get();
        $categories = SupplierCategory::orderBy('name')->get();

        return view('suppliers.edit', compact('supplier', 'accounts', 'categories'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate(
            [
                'name'     => 'required|string|max:255',
                'phone'    => 'nullable|string|max:50',
                'email'    => 'nullable|email|max:100',
                'category_id' => 'nullable|exists:suplier_categories,id',
                'is_active'   => 'required|boolean',
            ]
        );

        DB::transaction(function () use (&$validated, $supplier) {

            $oldCategoryId = $supplier->category_id;
            $newCategoryId = $validated['category_id'] ?? null;

            // 1) التعامل مع الحساب المرتبط
            if ($supplier->account_id) {
                // يوجد حساب مرتبط سابقاً → حدّث الاسم والوصف، وانقل إن تغيّر التصنيف
                $child = ChartOfAccount::find($supplier->account_id);

                if ($child) {
                    $child->name        = $validated['name'];
                    $child->description = 'حساب مورد: ' . $validated['name'];

                    // تغيّر التصنيف؟
                    if ($newCategoryId && $newCategoryId !== $oldCategoryId) {
                        $newCategory = SupplierCategory::select('id', 'account_id')->find($newCategoryId);
                        if ($newCategory && $newCategory->account_id) {
                            $newParent = ChartOfAccount::findOrFail($newCategory->account_id);
                            if (!$newParent->is_group) {
                                abort(422, 'حساب التصنيف الجديد يجب أن يكون "مجموعة".');
                            }

                            $newCode  = $this->generateNextCode($newParent->id);
                            $newLevel = $this->calculateLevel($newParent->id);

                            $child->parent_id       = $newParent->id;
                            $child->code            = $newCode;
                            $child->level           = $newLevel;
                            $child->account_type_id = $newParent->account_type_id;
                            $child->nature          = $newParent->nature;
                            $child->currency_id     = $newParent->currency_id;
                            $child->status          = $newParent->status ?? $child->status;

                            $child->is_group    = false;
                            $child->allow_entry = true;
                        }
                    }

                    $child->save();
                }
            } else {
                // لا يوجد حساب → أنشئ واحدًا تحت أب التصنيف (إن وجد)
                if ($newCategoryId) {
                    $newCategory = SupplierCategory::select('id', 'account_id')->find($newCategoryId);
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
                            'description'     => 'حساب مورد: ' . $validated['name'],
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
            //edit تسجيل في سجل التدقيق
            AuditTrailController::log(
                'suppliers',
                $supplier->id,
                'U',
                'تعديل بيانات المورد: ' . $supplier->name,
                Auth::id()
            );
            // 2) تحديث المورد
            $supplier->update($validated);
        });

        return redirect()->route('suppliers.index')->with('success', 'تم تحديث بيانات المورد والحساب المرتبط');
    }

    public function destroy(Supplier $supplier)
    {
        DB::transaction(function () use ($supplier) {

            $accountId = $supplier->account_id;

            // حذف ناعم للمورد
            $supplier->delete();

            // معالجة الحساب
            if ($accountId) {
                $account = ChartOfAccount::find($accountId);
                if ($account) {
                    // لو له أبناء (المفترض لا)
                    if ($account->children()->exists()) {
                        $account->status = 'غير نشط';
                        $account->save();
                        return;
                    }

                    // 🔎 (اختياري) فحص وجود حركات
                    $hasMovements = false;
                    // مثال: $hasMovements = $account->journalLines()->exists();

                    if ($hasMovements) {
                        $account->status = 'غير نشط';
                        $account->save();
                    } else {
                        $account->delete(); // حذف ناعم
                    }
                }
            }
            //delete تسجيل في سجل التدقيق
            AuditTrailController::log(
                'suppliers',
                $supplier->id,
                'D',
                'حذف بيانات المورد: ' . $supplier->name,
                Auth::id()
            );
        });

        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد ومعالجة حسابه');
    }

    public function restore($id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);

        DB::transaction(function () use ($supplier) {
            $supplier->restore();

            if ($supplier->account_id) {
                $account = ChartOfAccount::withTrashed()->find($supplier->account_id);
                if ($account && $account->trashed()) {
                    $account->restore();
                    $account->status = 'نشط';
                    $account->save();
                }
            }
            //edit تسجيل في سجل التدقيق
            AuditTrailController::log(
                'suppliers',
                $supplier->id,
                'U',
                'استعادة بيانات المورد: ' . $supplier->name,
                Auth::id()
            );
        });

        return redirect()->route('suppliers.index')->with('success', 'تم استرجاع المورد وحسابه');
    }

    /* ================= Helpers لتوليد كود الحساب ومستواه ================= */

    private function generateNextCode($parentId = null)
    {
        if (!$parentId) {
            $lastMain = ChartOfAccount::whereNull('parent_id')->orderBy('code', 'desc')->first();
            return $lastMain ? $lastMain->code + 1 : 1;
        }

        $parent = ChartOfAccount::findOrFail($parentId);
        $level  = $parent->level + 1;

        $lastChild = ChartOfAccount::where('parent_id', $parentId)->orderBy('code', 'desc')->first();

        if (!$lastChild) {
            if ($level == 2)      return $parent->code . '1';
            elseif ($level == 3) return $parent->code . '01';
            elseif ($level == 4) return $parent->code . '001';
            elseif ($level == 5) return $parent->code . '0001';
            elseif ($level == 6) return $parent->code . '00001';
        } else {
            return $lastChild->code + 1;
        }

        return null;
    }

    private function calculateLevel($parentId)
    {
        if (!$parentId) return 1;

        $parent = ChartOfAccount::find($parentId);
        return $parent ? $parent->level + 1 : 1;
    }
}
