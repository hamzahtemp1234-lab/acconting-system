<?php

namespace App\Http\Controllers;

use App\Models\CostCenterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class CostCenterTypeController extends Controller
{
    public function index()
    {
        $types = CostCenterType::all();
        return view('cost_center_types.index', compact('types'));
    }

    public function create()
    {
        return view('cost_center_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:cost_center_types,code',
            'name' => 'required|string|max:255',
        ]);

        $cost = CostCenterType::create($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'cost_center_types',
            $cost->id,
            'I',
            'أضافة نوع مراكز التكلفة: ' .  $cost->name,
            Auth::id()
        );
        return redirect()->route('cost-center-types.index')->with('success', 'تمت إضافة النوع بنجاح');
    }

    public function edit(CostCenterType $costCenterType)
    {
        return view('cost_center_types.edit', compact('costCenterType'));
    }

    public function update(Request $request, CostCenterType $costCenterType)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:cost_center_types,code,' . $costCenterType->id,
            'name' => 'required|string|max:255',
        ]);

        $costCenterType->update($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'cost_center_types',
            $costCenterType->id,
            'U',
            'تعديل نوع مراكز التكلفة: ' .  $costCenterType->name,
            Auth::id()
        );
        return redirect()->route('cost-center-types.index')->with('success', 'تم تحديث النوع بنجاح');
    }

    public function destroy(CostCenterType $costCenterType)
    {
        $costCenterType->delete();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'cost_center_types',
            $costCenterType->id,
            'D',
            'حذف نوع مراكز التكلفة: ' .  $costCenterType->name,
            Auth::id()
        );
        return redirect()->route('cost-center-types.index')->with('success', 'تم حذف النوع');
    }
}
