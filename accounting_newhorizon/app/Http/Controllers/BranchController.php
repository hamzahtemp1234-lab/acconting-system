<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $q = Branch::query();

        if ($search = $request->get('search')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $q->where('is_active', (bool)$request->get('is_active'));
        }

        $branches = $q->latest()->paginate(12)->withQueryString();

        // إحصائيات سريعة
        $stats = [
            'total' => Branch::count(),
            'active' => Branch::where('is_active', true)->count(),
            'inactive' => Branch::where('is_active', false)->count(),
        ];

        return view('branches.index', compact('branches', 'stats'));
    }

    public function create()
    {
        $nextCode = \App\Models\Branch::nextCode();
        return view('branches.create', compact('nextCode'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:20|unique:branches,code',
            'name'      => 'required|string|max:100',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $branch = Branch::create($data);
        //add  تسجيل في سجل التدقيق
        AuditTrailController::log(
            'branches',
            $branch->id,
            'I',
            'أضافة فرع جديد: ' . $branch->name,
            Auth::id()
        );
        return redirect()->route('branches.index')->with('success', 'تم إضافة الفرع بنجاح.');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'name'      => 'required|string|max:100',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $branch->update($data);
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'branches',
            $branch->id,
            'U',
            'تعديل بيانات الفرع: ' . $branch->name,
            Auth::id()
        );
        return redirect()->route('branches.index')->with('success', 'تم تحديث بيانات الفرع.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        //delete تسجيل في سجل التدقيق
        AuditTrailController::log(
            'branches',
            $branch->id,
            'D',
            'حذف بيانات الفرع: ' . $branch->name,
            Auth::id()
        );
        return redirect()->route('branches.index')->with('success', 'تم حذف الفرع.');
    }
}
