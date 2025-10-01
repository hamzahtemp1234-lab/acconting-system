<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function create($currencyId)
    {
        $currency = Currency::findOrFail($currencyId);
        return view('exchange_rates.create', compact('currency'));
    }

    public function store(Request $request, $currencyId)
    {
        $currency = Currency::findOrFail($currencyId);

        $request->validate([
            'rate' => 'required|numeric|min:0',
            'from_date_exchange' => 'required|date',
        ]);

        $currency->exchangeRates()->create([
            'rate' => $request->rate,
            'from_date_exchange' => $request->from_date_exchange,
        ]);
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'exchange_rates',
            $currency->exchangeRates()->id,
            'I',
            'تغيير سعر صرف العملة: ' .  $currency->name,
            Auth::id()
        );
        return redirect()->route('currencies.edit', $currency->id)
            ->with('success', 'تمت إضافة سعر الصرف بنجاح');
    }
}
