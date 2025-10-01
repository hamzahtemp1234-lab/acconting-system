<?php

namespace App\Http\Controllers;

use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
class FiscalYearController extends Controller
{
    public function index()
    {
        $fiscalYears = FiscalYear::all();
        return view('fiscal_years.index', compact('fiscalYears'));
    }

    public function create()
    {
        return view('fiscal_years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fiscal_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $fiscalYear = FiscalYear::create($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_years',
            $fiscalYear->id,
            'I',
            'أضافه سنه ماليه جديده: ' .  $fiscalYear->name,
            Auth::id()
        );
        return redirect()->route('fiscal-years.index')->with('success', 'تمت إضافة السنة المالية بنجاح');
    }

    public function edit(FiscalYear $fiscalYear)
    {
        return view('fiscal_years.edit', compact('fiscalYear'));
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fiscal_years,name,' . $fiscalYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $fiscalYear->update($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_years',
            $fiscalYear->id,
            'U',
            'تعديل السنه الماليه: ' .  $fiscalYear->name,
            Auth::id()
        );
        return redirect()->route('fiscal-years.index')->with('success', 'تم تحديث السنة المالية');
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        $fiscalYear->delete();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_years',
            $fiscalYear->id,
            'D',
            'حذف السنه الماليه: ' .  $fiscalYear->name,
            Auth::id()
        );
        return redirect()->route('fiscal-years.index')->with('success', 'تم حذف السنة المالية');
    }
}
