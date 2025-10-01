<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use App\Models\CostCenterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class CostCenterController extends Controller
{
    public function index()
    {
        $centers = CostCenter::with(['type', 'parent'])->get();
        return view('cost-centers.index', compact('centers'));
    }

    public function create()
    {
        $types = CostCenterType::all();
        $parents = CostCenter::where('is_group', true)->get();
        return view('cost-centers.create', compact('types', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:cost_center_types,id',
            'parent_id' => 'nullable|exists:cost_centers,id',
        ]);

        $level = $request->parent_id
            ? CostCenter::find($request->parent_id)->level + 1
            : 1;

        $costCenter = CostCenter::create([
            'code' => $request->code,
            'name' => $request->name,
            'type_id' => $request->type_id,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'is_group' => $request->has('is_group'),
            'is_active' => $request->has('is_active'),
        ]);
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'cost_centers',
            $costCenter->id,
            'I',
            'أضافة مركز التكلفة : ' . $costCenter->name,
            Auth::id()
        );
        return redirect()->route('cost-centers.index')->with('success', 'تمت إضافة مركز التكلفة بنجاح');
    }

    public function edit($id)
    {
        $center = CostCenter::findOrFail($id);
        $types = CostCenterType::all();
        $parents = CostCenter::where('is_group', true)->where('id', '!=', $id)->get();
        return view('cost-centers.edit', compact('center', 'types', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $center = CostCenter::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:20|unique:cost_centers,code,' . $center->id . ',id,type_id,' . $request->type_id,
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:cost_center_types,id',
            'parent_id' => 'nullable|exists:cost_centers,id',
        ]);

        $level = $request->parent_id
            ? CostCenter::find($request->parent_id)->level + 1
            : 1;

        $center->update([
            'code' => $request->code,
            'name' => $request->name,
            'type_id' => $request->type_id,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'is_group' => $request->has('is_group'),
            'is_active' => $request->has('is_active'),
        ]);
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'cost_centers',
            $center->id,
            'U',
            'تعديل مركز التكلفة : ' .  $center->name,
            Auth::id()
        );
        return redirect()->route('cost-centers.index')->with('success', 'تم تحديث مركز التكلفة بنجاح');
    }

    public function destroy($id)
    {
        $center = CostCenter::findOrFail($id);

        if ($center->children()->exists()) {
            return redirect()->route('cost-centers.index')
                ->with('error', 'لا يمكن حذف مركز يحتوي على مراكز فرعية');
        }

        $center->delete(); // ✅ SoftDelete
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'cost_centers',
            $center->id,
            'D',
            'حذف مركز التكلفة : ' .  $center->name,
            Auth::id()
        );
        return redirect()->route('cost-centers.index')->with('success', 'تم حذف مركز التكلفة بنجاح');
    }
}
