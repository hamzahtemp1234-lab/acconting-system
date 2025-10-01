<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Currency;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Support\AppSettings;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class AgentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $agents = Agent::with(['currency', 'account'])
            ->when($q, fn($qq) => $qq->where(function ($w) use ($q) {
                $w->where('code', 'like', "%$q%")
                    ->orWhere('name', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            }))
            ->orderBy('code')
            ->paginate(15);

        $stats = [
            'total'   => Agent::count(),
            'active'  => Agent::where('is_active', 1)->count(),
            'company' => Agent::where('type', 'company')->count(),
            'person'  => Agent::where('type', 'individual')->count(),
        ];

        return view('agents.index', compact('agents', 'stats', 'q'));
    }

    public function create()
    {
        $currencies = Currency::orderBy('code')->get();
        $accounts   = ChartOfAccount::orderBy('code')->get();         // لو حاب تختار حساب جاهز
        $parentGroups = ChartOfAccount::where('is_group', true)->get(); // لو حاب توليد حساب فرعي
        $nextCode   = Agent::nextCode();

        return view('agents.create', compact('currencies', 'accounts', 'parentGroups', 'nextCode'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => ['required', \Illuminate\Validation\Rule::in(['individual', 'company'])],
            'email'  => 'nullable|email|max:255',
            'phone'  => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city'   => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:100',
            'currency_id' => 'nullable|exists:currencies,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string',

            // اختياري للمستخدم:
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id', // أب يدوي
        ]);

        DB::transaction(function () use (&$validated, $request) {

            // 1) أولوية: حساب جاهز اختاره المستخدم
            if (!empty($validated['account_id'])) {
                // لا شيء
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
                    'description'     => 'حساب وكيل: ' . $validated['name'],
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
            // 3) إعداد عام للوكلاء
            else {
                $auto    = AppSettings::get('agents.auto_create_child_account', false);
                $parentId = AppSettings::get('agents.parent_account_id');
                if ($auto && $parentId) {
                    $parent = ChartOfAccount::findOrFail($parentId);
                    abort_unless($parent->is_group, 422, 'إعداد الأب للوكلاء يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);

                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $code,
                        'name'            => $validated['name'],
                        'description'     => 'حساب وكيل: ' . $validated['name'],
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

            $agent = \App\Models\Agent::create($validated);
            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'agents',
                $agent->id,
                'I',
                'أضافة وكيل جديد: ' . $agent->name,
                Auth::id()
            );
        });

        return redirect()->route('agents.index')->with('success', 'تم إضافة الوكيل بنجاح');
    }


    public function edit(Agent $agent)
    {
        $currencies = Currency::orderBy('code')->get();
        $accounts   = ChartOfAccount::orderBy('code')->get();
        $parentGroups = ChartOfAccount::where('is_group', true)->get();

        return view('agents.edit', compact('agent', 'currencies', 'accounts', 'parentGroups'));
    }

    public function update(Request $request, \App\Models\Agent $agent)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => ['required', \Illuminate\Validation\Rule::in(['individual', 'company'])],
            'email'  => 'nullable|email|max:255',
            'phone'  => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city'   => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:100',
            'currency_id' => 'nullable|exists:currencies,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string',

            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        DB::transaction(function () use (&$validated, $request, $agent) {

            // إن عنده حساب: حدّث الاسم/الوصف
            if ($agent->account_id) {
                $acc = ChartOfAccount::find($agent->account_id);
                if ($acc) {
                    $acc->name = $validated['name'];
                    $acc->description = 'حساب وكيل: ' . $validated['name'];
                    $acc->save();
                }
            }
            // ما عنده حساب: وفّر واحدًا بنفس أولوية الإنشاء
            else {
                if (!empty($validated['account_id'])) {
                    // استخدم الحساب المختار
                } elseif ($request->filled('parent_account_id')) {
                    $parent = ChartOfAccount::findOrFail($request->parent_account_id);
                    abort_unless($parent->is_group, 422, 'الحساب الأب يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);
                    $child = ChartOfAccount::create([
                        'parent_id' => $parent->id,
                        'code' => $code,
                        'name' => $validated['name'],
                        'description' => 'حساب وكيل: ' . $validated['name'],
                        'account_type_id' => $parent->account_type_id,
                        'nature' => $parent->nature,
                        'is_group' => false,
                        'level' => $level,
                        'currency_id' => $parent->currency_id,
                        'allow_entry' => true,
                        'is_default' => false,
                        'status' => $parent->status ?? 'نشط',
                    ]);
                    $validated['account_id'] = $child->id;
                } else {
                    $auto = AppSettings::get('agents.auto_create_child_account', false);
                    $parentId = AppSettings::get('agents.parent_account_id');
                    if ($auto && $parentId) {
                        $parent = ChartOfAccount::findOrFail($parentId);
                        abort_unless($parent->is_group, 422, 'إعداد الأب للوكلاء يجب أن يكون مجموعة.');
                        $code  = $this->generateNextCode($parent->id);
                        $level = $this->calculateLevel($parent->id);
                        $child = ChartOfAccount::create([
                            'parent_id' => $parent->id,
                            'code' => $code,
                            'name' => $validated['name'],
                            'description' => 'حساب وكيل: ' . $validated['name'],
                            'account_type_id' => $parent->account_type_id,
                            'nature' => $parent->nature,
                            'is_group' => false,
                            'level' => $level,
                            'currency_id' => $parent->currency_id,
                            'allow_entry' => true,
                            'is_default' => false,
                            'status' => $parent->status ?? 'نشط',
                        ]);
                        $validated['account_id'] = $child->id;
                    }
                }
            }

            $agent->update($validated);
            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'agents',
                $agent->id,
                'U',
                'تعديل بيانات الوكيل : ' . $agent->name,
                Auth::id()
            );
        });

        return redirect()->route('agents.index')->with('success', 'تم تحديث بيانات الوكيل');
    }


    public function destroy(Agent $agent)
    {
        DB::transaction(function () use ($agent) {
            // حذف ناعم للAgent
            $agent->delete();

            // (اختياري) عطل حسابه أو احذفه ناعمًا إن لا توجد حركات
            if ($agent->account_id) {
                $acc = ChartOfAccount::find($agent->account_id);
                if ($acc) {
                    // إن عندك علاقة للحركات فعلها هنا للتحقق
                    $hasMovements = false;
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
            'agents',
            $agent->id,
            'D',
            'حذف وكيل: ' . $agent->name,
            Auth::id()
        );
        return redirect()->route('agents.index')->with('success', 'تم حذف الوكيل ومعالجة حسابه');
    }

    /* ======= نفس منطقك في ChartOfAccountController للتسلسل والمستوى ======= */

    private function generateNextCode($parentId = null)
    {
        if (!$parentId) {
            $lastMain = ChartOfAccount::whereNull('parent_id')->orderBy('code', 'desc')->first();
            return $lastMain ? $lastMain->code + 1 : 1;
        }

        $parent = ChartOfAccount::findOrFail($parentId);
        $level = $parent->level + 1;

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
