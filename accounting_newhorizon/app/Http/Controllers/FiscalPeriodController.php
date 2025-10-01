<?php

namespace App\Http\Controllers;

use App\Models\FiscalPeriod;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
use Illuminate\Http\Request;

class FiscalPeriodController extends Controller
{
    public function index()
    {
        $periods = FiscalPeriod::with('fiscalYear')
            ->orderBy('fiscal_year_id')->orderBy('period_no')->get();

        return view('fiscal_periods.index', compact('periods'));
    }

    public function create()
    {
        $fiscalYears = FiscalYear::all();
        return view('fiscal_periods.create', compact('fiscalYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'period_no' => 'required|integer|min:1|max:13',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_closed' => 'nullable|boolean',
        ]);

        // البحث عن الفترة سواء محذوفة أو غير محذوفة
        $period = FiscalPeriod::withTrashed()
            ->where('fiscal_year_id', $request->fiscal_year_id)
            ->where('period_no', $request->period_no)
            ->first();

        if ($period) {
            if ($period->trashed()) {
                // ✅ استعادة الفترة المحذوفة
                $period->restore();

                // ✅ تحديث بياناتها بالقيم الجديدة من الفورم
                $period->update([
                    'fiscal_year_id' => $request->fiscal_year_id,
                    'period_no'      => $request->period_no,
                    'start_date'     => $request->start_date,
                    'end_date'       => $request->end_date,
                    'is_closed'      => $request->has('is_closed') ? 1 : 0,
                ]);
                // تسجيل في سجل التدقيق
                AuditTrailController::log(
                    'fiscal_periods',
                    $period->id,
                    'I',
                    'أضافة الفترات المحاسبية: ' .  $period->name,
                    Auth::id()
                );
                return redirect()->route('fiscal-periods.index')
                    ->with('success', 'تمت استعادة الفترة وتحديث بياناتها بنجاح');
            } else {
                // ❌ الفترة موجودة وغير محذوفة → خطأ
                return back()->withErrors([
                    'period_no' => 'الفترة رقم ' . $request->period_no . ' موجودة مسبقًا لهذه السنة المالية'
                ])->withInput();
            }
        }

        // ✅ إذا لم تكن موجودة → إنشاء فترة جديدة
        $period = FiscalPeriod::create([
            'fiscal_year_id' => $request->fiscal_year_id,
            'period_no'      => $request->period_no,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_closed'      => $request->has('is_closed') ? 1 : 0,
        ]);
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_periods',
            $period->id,
            'I',
            'أضافة الفترة المحاسبية: ' .  $period->name,
            Auth::id()
        );
        return redirect()->route('fiscal-periods.index')->with('success', 'تمت إضافة الفترة المالية بنجاح');
    }


    public function edit(FiscalPeriod $fiscalPeriod)
    {
        $fiscalYears = FiscalYear::all();
        return view('fiscal_periods.edit', compact('fiscalPeriod', 'fiscalYears'));
    }

    public function update(Request $request, FiscalPeriod $fiscalPeriod)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'period_no' => 'required|integer|min:1|max:13',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if (FiscalPeriod::where('fiscal_year_id', $request->fiscal_year_id)
            ->where('period_no', $request->period_no)
            ->where('id', '!=', $fiscalPeriod->id)->exists()
        ) {
            return back()->withErrors(['period_no' => 'هذه الفترة موجودة مسبقًا لهذه السنة المالية']);
        }

        $fiscalPeriod->update($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_periods',
            $fiscalPeriod->id,
            'U',
            'تعديل الفترة المحاسبية: ' .  $fiscalPeriod->name,
            Auth::id()
        );
        return redirect()->route('fiscal-periods.index')->with('success', 'تم تحديث الفترة المالية');
    }

    public function destroy(FiscalPeriod $fiscalPeriod)
    {
        $fiscalPeriod->delete();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_periods',
            $fiscalPeriod->id,
            'D',
            'حذف الفترة المحاسبية: ' .  $fiscalPeriod->name,
            Auth::id()
        );
        return redirect()->route('fiscal-periods.index')->with('success', 'تم حذف الفترة المالية (Soft Delete)');
    }

    public function restore($id)
    {
        $fiscalPeriod = FiscalPeriod::withTrashed()->findOrFail($id);
        $fiscalPeriod->restore();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_periods',
            $fiscalPeriod->id,
            'U',
            'استعادة الفترة المحاسبية: ' .  $fiscalPeriod->name,
            Auth::id()
        );
        return redirect()->route('fiscal-periods.index')->with('success', 'تم استعادة الفترة المالية');
    }

    public function forceDelete($id)
    {
        $fiscalPeriod = FiscalPeriod::withTrashed()->findOrFail($id);
        $fiscalPeriod->forceDelete();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'fiscal_periods',
            $fiscalPeriod->id,
            'D',
            'حذف الفترة المحاسبية نهائياً: ' .  $fiscalPeriod->name,
            Auth::id()
        );
        return redirect()->route('fiscal-periods.index')->with('success', 'تم حذف الفترة المالية نهائيًا');
    }
}
