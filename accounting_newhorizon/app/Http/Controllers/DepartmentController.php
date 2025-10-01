<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $qText = $request->get('q', '');

        $query = \App\Models\Department::with(['branch', 'manager']);

        if ($qText !== '') {
            $query->where(function ($w) use ($qText) {
                $w->where('code', 'like', "%{$qText}%")
                    ->orWhere('name', 'like', "%{$qText}%")
                    ->orWhereHas('branch', fn($b) => $b->where('name', 'like', "%{$qText}%"))
                    ->orWhereHas('manager', fn($m) => $m->where('name', 'like', "%{$qText}%"));
            });
        }

        $departments = $query->latest()->paginate(12);

        $stats = [
            'total'           => \App\Models\Department::count(),
            'with_manager'    => \App\Models\Department::whereNotNull('manager_id')->count(),
            'without_manager' => \App\Models\Department::whereNull('manager_id')->count(),
        ];

        return view('departments.index', compact('departments', 'stats') + ['q' => $qText]);
    }


    // app/Http/Controllers/DepartmentController.php

    public function create()
    {
        $branches  = \App\Models\Branch::orderBy('name')->get();
        // اختيارياً: لو فتحت الصفحة مع ?branch_id=.. بنجيب اقتراح مبدئي:
        //$branchId  = request()->integer('branch_id');
        $nextCode  =   \App\Models\Department::nextCode();

        // اختيارياً: قائمة المديرين
        $employees = \App\Models\Employee::orderBy('name')->limit(1000)->get();

        return view('departments.create', compact('branches', 'nextCode', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id'  => 'required|exists:branches,id',
            'name'       => 'required|string|max:100',
            'manager_id' => 'nullable|exists:employees,id',
            // لا تستقبل 'code' من المستخدم
        ]);

        // توليد احتياطي (قبل create) — أمان إضافي
        if (empty($request->input('code'))) {
            $data['code'] = \App\Models\Department::nextCode((int)$data['branch_id']);
        }
        $department = "";
        // في حال سباق الكتابة، نعيد المحاولة مرة ثانية لو حصل تعارض UNIQUE(branch_id, code)
        try {
            $department = \App\Models\Department::create($data); // مهم: create (ليس insert)
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'departments_branch_id_code_unique')) {
                $department =  $data['code'] = \App\Models\Department::nextCode((int)$data['branch_id']);
                \App\Models\Department::create($data);
            } else {
                throw $e;
            }
        }
        //add  تسجيل في سجل التدقيق
        AuditTrailController::log(
            'departments',
            $department->id,
            'I',
            'أضافة قسم جديد: ' . $department->name,
            Auth::id()
        );
        return redirect()->route('departments.index')->with('success', 'تم إضافة القسم بنجاح.');
    }



    public function edit(Department $department)
    {
        // اختيارياً: قائمة المديرين
        $employees = \App\Models\Employee::orderBy('name')->limit(1000)->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('departments.edit', compact('department', 'branches', 'employees'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'branch_id'  => 'required|exists:branches,id',
            'code'       => 'required|string|max:20',
            'name'       => 'required|string|max:100',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        $exists = Department::where('branch_id', $data['branch_id'])
            ->where('code', $data['code'])
            ->where('id', '!=', $department->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['code' => 'هذا الرمز مستخدم داخل نفس الفرع.'])->withInput();
        }

        $department->update($data);
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'departments',
            $department->id,
            'U',
            'تعديل بيانات القسم: ' . $department->name,
            Auth::id()
        );
        return redirect()->route('departments.index')->with('success', 'تم تحديث بيانات القسم.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        //delete تسجيل في سجل التدقيق
        AuditTrailController::log(
            'departments',
            $department->id,
            'D',
            'حذف بيانات القسم: ' . $department->name,
            Auth::id()
        );
        return redirect()->route('departments.index')->with('success', 'تم حذف القسم.');
    }
}
