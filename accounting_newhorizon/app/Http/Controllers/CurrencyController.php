<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
use App\Models\Currency;


class CurrencyController extends Controller
{
    // عرض جميع العملات
    public function index()
    {
        $currencies = Currency::with(['exchangeRates' => function ($q) {
            $q->latest('from_date_exchange');
        }])->get();

        return view('currencies.index', compact('currencies'));
    }


    // نموذج إنشاء عملة جديدة
    public function create()
    {
        return view('currencies.create');
    }


    // حفظ عملة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
        ]);


        $currencies = Currency::create($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'currencies',
            $currencies->id,
            'I',
            'أضافة عملة جديده: ' .  $currencies->name,
            Auth::id()
        );

        return redirect()->route('currencies.index')
            ->with('success', 'تم إضافة العملة بنجاح');
    }


    // نموذج تعديل العملة
    public function edit($id)
    {
        $currency = Currency::with('exchangeRates')->findOrFail($id);
        return view('currencies.edit', compact('currency'));
    }


    // تحديث بيانات العملة
    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);


        $request->validate([
            'code' => 'required|string|max:5|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
        ]);


        $currency->update($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'currencies',
            $currency->id,
            'U',
            'تعديل العملة: ' .  $currency->name,
            Auth::id()
        );

        return redirect()->route('currencies.index')
            ->with('success', 'تم تحديث العملة بنجاح');
    }


    // حذف عملة (Soft Delete)
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->delete();

        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'currencies',
            $currency->id,
            'D',
            'حذف العملة: ' .  $currency->name,
            Auth::id()
        );
        return redirect()->route('currencies.index')
            ->with('success', 'تم حذف العملة بنجاح');
    }
}
