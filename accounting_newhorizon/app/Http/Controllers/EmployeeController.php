<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\AppSettings;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $qText = $request->get('q', '');

        $q = Employee::with(['department']);

        if ($qText !== '') {
            $q->where(function ($w) use ($qText) {
                $w->where('code', 'like', "%{$qText}%")
                    ->orWhere('name', 'like', "%{$qText}%")
                    ->orWhere('phone', 'like', "%{$qText}%")
                    ->orWhere('email', 'like', "%{$qText}%")
                    ->orWhereHas('department', fn($d) => $d->where('name', 'like', "%{$qText}%"));
            });
        }

        $employees = $q->latest()->paginate(12);

        $stats = [
            'total'              => Employee::count(),
            'with_department'    => Employee::whereNotNull('department_id')->count(),
            'without_department' => Employee::whereNull('department_id')->count(),
        ];

        return view('employees.index', compact('employees', 'stats') + ['q' => $qText]);
    }

    public function create()
    {
        $departments  = Department::orderBy('name')->get();
        $accounts     = ChartOfAccount::orderBy('code')->get();            // اختيار حساب جاهز
        $parentGroups = ChartOfAccount::where('is_group', true)->get();    // اختيار أب لتوليد حساب فرعي
        $nextCode     = Employee::nextCode();

        return view('employees.create', compact('departments', 'accounts', 'parentGroups', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // لا نطلب code؛ يتولّد من الموديل
            'name'          => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'account_id'    => 'nullable|exists:chart_of_accounts,id',     // حساب جاهز
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id', // أب لتوليد حساب فرعي
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:100|unique:employees,email',
        ]);

        DB::transaction(function () use (&$validated, $request) {

            // 1) أولوية: حساب جاهز اختاره المستخدم
            if (!empty($validated['account_id'])) {
                // لا شيء؛ سنستخدمه كما هو
            }
            // 2) أب يدوي من الفورم → أنشئ حساب فرعي باسم الموظف
            elseif ($request->filled('parent_account_id')) {
                $parent = ChartOfAccount::findOrFail($request->parent_account_id);
                abort_unless($parent->is_group, 422, 'الحساب الأب يجب أن يكون مجموعة.');
                $code  = $this->generateNextCode($parent->id);
                $level = $this->calculateLevel($parent->id);

                $child = ChartOfAccount::create([
                    'parent_id'       => $parent->id,
                    'code'            => $code,
                    'name'            => $validated['name'],
                    'description'     => 'حساب موظف: ' . $validated['name'],
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
            // 3) إعداد عام للموظفين (مثل الوكلاء)
            else {
                $auto    = AppSettings::get('employees.auto_create_child_account', false);
                $parentId = AppSettings::get('employees.parent_account_id');
                if ($auto && $parentId) {
                    $parent = ChartOfAccount::findOrFail($parentId);
                    abort_unless($parent->is_group, 422, 'إعداد الأب للموظفين يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);

                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $code,
                        'name'            => $validated['name'],
                        'description'     => 'حساب موظف: ' . $validated['name'],
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

            $employee = Employee::create($validated); // مهم: Eloquent triggers ->creating لتوليد code
            //add  تسجيل في سجل التدقيق
            AuditTrailController::log(
                'employees',
                $employee->id,
                'I',
                'أضافة موظف جديد: ' . $employee->name,
                Auth::id()
            );
        });

        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function edit(Employee $employee)
    {
        $departments  = Department::orderBy('name')->get();
        $accounts     = ChartOfAccount::orderBy('code')->get();
        $parentGroups = ChartOfAccount::where('is_group', true)->get();

        return view('employees.edit', compact('employee', 'departments', 'accounts', 'parentGroups'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            // code لا يُحدّث من الواجهة
            'name'          => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'account_id'    => 'nullable|exists:chart_of_accounts,id',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:100|unique:employees,email,' . $employee->id,
        ]);

        DB::transaction(function () use (&$validated, $request, $employee) {

            // لو عنده حساب مرتبط بالفعل، نُحدّث الاسم/الوصف
            if ($employee->account_id) {
                $acc = ChartOfAccount::find($employee->account_id);
                if ($acc) {
                    $acc->name        = $validated['name'];
                    $acc->description = 'حساب موظف: ' . $validated['name'];
                    $acc->save();
                }
            }
            // لا يملك حسابًا → وفّر واحدًا بنفس أولوية الإنشاء
            else {
                if (!empty($validated['account_id'])) {
                    // استخدم الحساب المختار كما هو
                } elseif ($request->filled('parent_account_id')) {
                    $parent = ChartOfAccount::findOrFail($request->parent_account_id);
                    abort_unless($parent->is_group, 422, 'الحساب الأب يجب أن يكون مجموعة.');
                    $code  = $this->generateNextCode($parent->id);
                    $level = $this->calculateLevel($parent->id);
                    $child = ChartOfAccount::create([
                        'parent_id'       => $parent->id,
                        'code'            => $code,
                        'name'            => $validated['name'],
                        'description'     => 'حساب موظف: ' . $validated['name'],
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
                    $auto    = AppSettings::get('employees.auto_create_child_account', false);
                    $parentId = AppSettings::get('employees.parent_account_id');
                    if ($auto && $parentId) {
                        $parent = ChartOfAccount::findOrFail($parentId);
                        abort_unless($parent->is_group, 422, 'إعداد الأب للموظفين يجب أن يكون مجموعة.');
                        $code  = $this->generateNextCode($parent->id);
                        $level = $this->calculateLevel($parent->id);
                        $child = ChartOfAccount::create([
                            'parent_id'       => $parent->id,
                            'code'            => $code,
                            'name'            => $validated['name'],
                            'description'     => 'حساب موظف: ' . $validated['name'],
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

            $employee->update($validated);
            //edit تسجيل في سجل التدقيق
            AuditTrailController::log(
                'employees',
                $employee->id,
                'U',
                'تعديل بيانات الموظف: ' . $employee->name,
                Auth::id()
            );
        });

        return redirect()->route('employees.index')->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            $employee->delete();

            // (اختياري) تعامل مع حسابه
            if ($employee->account_id) {
                $acc = ChartOfAccount::find($employee->account_id);
                if ($acc) {
                    // TODO: ضع منطق التحقق من وجود حركات
                    $hasMovements = false;
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
                'employees',
                $employee->id,
                'D',
                'حذف بيانات الموظف: ' . $employee->name,
                Auth::id()
            );
        });

        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف ومعالجة حسابه.');
    }

    /* ======= نفس Helpers تبع الوكلاء ======= */

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
