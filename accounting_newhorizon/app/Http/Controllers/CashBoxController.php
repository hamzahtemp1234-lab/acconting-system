<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\Currency;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\AppSettings;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class CashBoxController extends Controller
{
    public function index(Request $request)
    {
        $qText = $request->get('q', '');

        $q = CashBox::with(['currency', 'account', 'branch', 'keeper']);

        if ($qText !== '') {
            $q->where(function ($w) use ($qText) {
                $w->where('code', 'like', "%{$qText}%")
                    ->orWhere('name', 'like', "%{$qText}%")
                    ->orWhere('notes', 'like', "%{$qText}%");
            });
        }

        $cashboxes = $q->orderBy('code')->paginate(15);

        $stats = [
            'total'   => CashBox::count(),
            'active'  => CashBox::where('is_active', 1)->count(),
        ];

        return view('cashboxes.index', compact('cashboxes', 'stats') + ['q' => $qText]);
    }

    public function create()
    {
        $currencies   = Currency::orderBy('code')->get();
        $branches     = Branch::orderBy('name')->get();
        $keepers      = Employee::orderBy('name')->get();
        $accounts     = ChartOfAccount::orderBy('code')->get();              // اختيار حساب جاهز
        $parentGroups = ChartOfAccount::where('is_group', true)->get();      // اختيار أب لتوليد حساب فرعي
        $nextCode     = CashBox::nextCode();

        return view('cashboxes.create', compact('currencies', 'branches', 'keepers', 'accounts', 'parentGroups', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'currency_id'        => 'nullable|exists:currencies,id',
            'branch_id'          => 'nullable|exists:branches,id',
            'keeper_employee_id' => 'nullable|exists:employees,id',
            'is_active'          => 'required|boolean',
            'notes'              => 'nullable|string',

            'account_id'        => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        DB::transaction(function () use (&$validated, $request) {

            if (!empty($validated['account_id'])) {
                // استخدمه كما هو
            } elseif ($request->filled('parent_account_id')) {
                $parent = ChartOfAccount::findOrFail($request->parent_account_id);
                abort_unless($parent->is_group, 422, 'الحساب الأب يجب أن يكون مجموعة.');
                $code  = $this->generateNextCode($parent->id);
                $level = $this->calculateLevel($parent->id);

                $child = ChartOfAccount::create([
                    'parent_id'       => $parent->id,
                    'code'            => $code,
                    'name'            => $validated['name'],
                    'description'     => 'حساب صندوق: ' . $validated['name'],
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
            } else {
                $auto     = AppSettings::get('cashboxes.auto_create_child_account', false);
                $parentId = AppSettings::get('cashboxes.parent_account_id');
                if ($auto && $parentId) {
                    $parent = ChartOfAccount::findOrFail($parentId);
                    abort_unless($parent->is_group, 422, 'إعداد الأب للصناديق يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);

                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $code,
                        'name'            => $validated['name'],
                        'description'     => 'حساب صندوق: ' . $validated['name'],
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

            $cashbox = CashBox::create($validated);
            //add  تسجيل في سجل التدقيق
            AuditTrailController::log(
                'chashbox',
                $cashbox->id,
                'I',
                'أضافة صندوق جديد: ' . $cashbox->name,
                Auth::id()
            );
        });

        return redirect()->route('cash-boxes.index')->with('success', 'تم إضافة الصندوق بنجاح.');
    }

    public function edit(CashBox $cashbox)
    {
        $currencies   = Currency::orderBy('code')->get();
        $branches     = Branch::orderBy('name')->get();
        $keepers      = Employee::orderBy('name')->get();
        $accounts     = ChartOfAccount::orderBy('code')->get();
        $parentGroups = ChartOfAccount::where('is_group', true)->get();

        return view('cashboxes.edit', compact('cashbox', 'currencies', 'branches', 'keepers', 'accounts', 'parentGroups'));
    }

    public function update(Request $request, CashBox $cashbox)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'currency_id'        => 'nullable|exists:currencies,id',
            'branch_id'          => 'nullable|exists:branches,id',
            'keeper_employee_id' => 'nullable|exists:employees,id',
            'is_active'          => 'required|boolean',
            'notes'              => 'nullable|string',

            'account_id'        => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        DB::transaction(function () use (&$validated, $request, $cashbox) {

            if ($cashbox->account_id) {
                $acc = ChartOfAccount::find($cashbox->account_id);
                if ($acc) {
                    $acc->name        = $validated['name'];
                    $acc->description = 'حساب صندوق: ' . $validated['name'];
                    $acc->save();
                }
            } else {
                if (!empty($validated['account_id'])) {
                    // استخدم المختار
                } elseif ($request->filled('parent_account_id')) {
                    $parent = ChartOfAccount::findOrFail($request->parent_account_id);
                    abort_unless($parent->is_group, 422, 'الحساب الأب يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);
                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $code,
                        'name'            => $validated['name'],
                        'description'     => 'حساب صندوق: ' . $validated['name'],
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
                } else {
                    $auto     = AppSettings::get('cashboxes.auto_create_child_account', false);
                    $parentId = AppSettings::get('cashboxes.parent_account_id');
                    if ($auto && $parentId) {
                        $parent = ChartOfAccount::findOrFail($parentId);
                        abort_unless($parent->is_group, 422, 'إعداد الأب للصناديق يجب أن يكون مجموعة.');
                        $code  = $this->generateNextCode($parent->id);
                        $level = $this->calculateLevel($parent->id);
                        $child = ChartOfAccount::create([
                            'parent_id'       => $parent->id,
                            'code'            => $code,
                            'name'            => $validated['name'],
                            'description'     => 'حساب صندوق: ' . $validated['name'],
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

            $cashbox->update($validated);
            //edit تسجيل في سجل التدقيق
            AuditTrailController::log(
                'cashbox',
                $cashbox->id,
                'U',
                'تعديل بيانات الصندوق: ' . $cashbox->name,
                Auth::id()
            );
        });

        return redirect()->route('cash-boxes.index')->with('success', 'تم تحديث بيانات الصندوق.');
    }

    public function destroy(CashBox $cashbox)
    {
        DB::transaction(function () use ($cashbox) {
            $cashbox->delete();

            if ($cashbox->account_id) {
                $acc = ChartOfAccount::find($cashbox->account_id);
                if ($acc) {
                    $hasMovements = false; // TODO: ضع منطقك
                    if ($hasMovements) {
                        $acc->status = 'غير نشط';
                        $acc->save();
                    } else {
                        $acc->delete();
                    }
                }
            }
            //delete تسجيل في سجل التدقيق
            AuditTrailController::log(
                'cashbox',
                $cashbox->id,
                'D',
                'حذف بيانات الصندوق: ' . $cashbox->name,
                Auth::id()
            );
        });

        return redirect()->route('cash-boxes.index')->with('success', 'تم حذف الصندوق ومعالجة حسابه.');
    }

    /* ===== Helpers ===== */
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
