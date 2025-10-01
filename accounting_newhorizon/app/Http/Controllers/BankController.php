<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Currency;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\AppSettings;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class BankController extends Controller
{
    public function index(Request $request)
    {
        $qText = $request->get('q', '');

        $q = Bank::with(['currency', 'account', 'branch']);

        if ($qText !== '') {
            $q->where(function ($w) use ($qText) {
                $w->where('code', 'like', "%{$qText}%")
                    ->orWhere('name', 'like', "%{$qText}%")
                    ->orWhere('phone', 'like', "%{$qText}%")
                    ->orWhere('iban', 'like', "%{$qText}%")
                    ->orWhere('swift', 'like', "%{$qText}%");
            });
        }

        $banks = $q->orderBy('code')->paginate(15);

        $stats = [
            'total'   => Bank::count(),
            'active'  => Bank::where('is_active', 1)->count(),
        ];

        return view('banks.index', compact('banks', 'stats') + ['q' => $qText]);
    }

    public function create()
    {
        $currencies   = Currency::orderBy('code')->get();
        $branches     = Branch::orderBy('name')->get();
        $accounts     = ChartOfAccount::orderBy('code')->get();              // اختيار حساب جاهز
        $parentGroups = ChartOfAccount::where('is_group', true)->get();      // اختيار أب لتوليد حساب فرعي
        $nextCode     = Bank::nextCode();

        return view('banks.create', compact('currencies', 'branches', 'accounts', 'parentGroups', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'branch_id'   => 'nullable|exists:branches,id',
            'iban'        => 'nullable|string|max:64',
            'swift'       => 'nullable|string|max:32',
            'contact_name' => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:255',
            'is_active'   => 'required|boolean',
            'notes'       => 'nullable|string',

            // الحسابات
            'account_id'        => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        DB::transaction(function () use (&$validated, $request) {
            // 1) حساب جاهز
            if (!empty($validated['account_id'])) {
                // استخدمه كما هو
            }
            // 2) أب يدوي من الفورم → أنشئ حساب فرعي
            elseif ($request->filled('parent_account_id')) {
                $parent = ChartOfAccount::findOrFail($request->parent_account_id);
                abort_unless($parent->is_group, 422, 'الحساب الأب يجب أن يكون مجموعة.');
                $code  = $this->generateNextCode($parent->id);
                $level = $this->calculateLevel($parent->id);

                $child = ChartOfAccount::create([
                    'parent_id'       => $parent->id,
                    'code'            => $code,
                    'name'            => $validated['name'],
                    'description'     => 'حساب بنك: ' . $validated['name'],
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
            // 3) إعداد عام للبنوك
            else {
                $auto     = AppSettings::get('banks.auto_create_child_account', false);
                $parentId = AppSettings::get('banks.parent_account_id');
                if ($auto && $parentId) {
                    $parent = ChartOfAccount::findOrFail($parentId);
                    abort_unless($parent->is_group, 422, 'إعداد الأب للبنوك يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);

                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $code,
                        'name'            => $validated['name'],
                        'description'     => 'حساب بنك: ' . $validated['name'],
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

            $bank = Bank::create($validated); // سيولّد code تلقائيًا في الموديل إن تركته فارغًا
            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'banks',
                $bank->id,
                'I',
                'أضافة بنك جديد: ' . $bank->name,
                Auth::id()
            );
        });

        return redirect()->route('banks.index')->with('success', 'تم إضافة البنك بنجاح.');
    }

    public function edit(Bank $bank)
    {
        $currencies   = Currency::orderBy('code')->get();
        $branches     = Branch::orderBy('name')->get();
        $accounts     = ChartOfAccount::orderBy('code')->get();
        $parentGroups = ChartOfAccount::where('is_group', true)->get();

        return view('banks.edit', compact('bank', 'currencies', 'branches', 'accounts', 'parentGroups'));
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'branch_id'   => 'nullable|exists:branches,id',
            'iban'        => 'nullable|string|max:64',
            'swift'       => 'nullable|string|max:32',
            'contact_name' => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:255',
            'is_active'   => 'required|boolean',
            'notes'       => 'nullable|string',

            'account_id'        => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        DB::transaction(function () use (&$validated, $request, $bank) {

            // لو لديه حساب مسبقًا → حدّث اسمه/وصفه
            if ($bank->account_id) {
                $acc = ChartOfAccount::find($bank->account_id);
                if ($acc) {
                    $acc->name        = $validated['name'];
                    $acc->description = 'حساب بنك: ' . $validated['name'];
                    $acc->save();
                }
            }
            // لا يملك حسابًا → نفس أولويات الإنشاء
            else {
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
                        'description'     => 'حساب بنك: ' . $validated['name'],
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
                    $auto     = AppSettings::get('banks.auto_create_child_account', false);
                    $parentId = AppSettings::get('banks.parent_account_id');
                    if ($auto && $parentId) {
                        $parent = ChartOfAccount::findOrFail($parentId);
                        abort_unless($parent->is_group, 422, 'إعداد الأب للبنوك يجب أن يكون مجموعة.');
                        $code  = $this->generateNextCode($parent->id);
                        $level = $this->calculateLevel($parent->id);
                        $child = ChartOfAccount::create([
                            'parent_id'       => $parent->id,
                            'code'            => $code,
                            'name'            => $validated['name'],
                            'description'     => 'حساب بنك: ' . $validated['name'],
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

            $bank->update($validated);
            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'banks',
                $bank->id,
                'U',
                'تعديل بيانات البنك: ' . $bank->name,
                Auth::id()
            );
        });

        return redirect()->route('banks.index')->with('success', 'تم تحديث بيانات البنك.');
    }

    public function destroy(Bank $bank)
    {
        DB::transaction(function () use ($bank) {
            $bank->delete();

            if ($bank->account_id) {
                $acc = ChartOfAccount::find($bank->account_id);
                if ($acc) {
                    $hasMovements = false; // TODO: ضع منطق التحقق
                    if ($hasMovements) {
                        $acc->status = 'غير نشط';
                        $acc->save();
                    } else {
                        $acc->delete();
                    }
                }
            }
        });
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'banks',
            $bank->id,
            'D',
            'حذف بيانات البنك: ' . $bank->name,
            Auth::id()
        );
        return redirect()->route('banks.index')->with('success', 'تم حذف البنك ومعالجة حسابه.');
    }

    /* ===== Helpers (نفس منطقك) ===== */
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
