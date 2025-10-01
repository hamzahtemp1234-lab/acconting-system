<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class SystemSettingController extends Controller
{
    //
    // عرض إعدادات النظام
    public function index()
    {
        $setting = SystemSetting::with('currency')->first();

        if (!$setting) {
            return redirect()->route('system-settings.setup')
                ->with('info', 'يرجى إعداد النظام أولاً قبل الاستخدام.');
        }

        // عرض الإعدادات إذا موجودة
        return view('system-settings.index', compact('setting'));
    }
    /**
     * عرض صفحة إعداد النظام
     */
    public function setup()
    {
        // التحقق إذا كان النظام مُعداً مسبقاً
        $existingSettings = SystemSetting::first();
        if ($existingSettings) {
            return redirect()->route('system-settings.index')
                ->with('info', 'النظام مُعد مسبقاً.');
        }

        $currencies = Currency::all();
        return view('system-settings.setup', compact('currencies'));
    }

    /**
     * حفظ إعدادات النظام الأولية
     */
    public function store(Request $request)
    {
        // التحقق إذا كان النظام مُعداً مسبقاً
        $existingSettings = SystemSetting::first();
        if ($existingSettings) {
            return redirect()->route('system-settings.index')
                ->with('error', 'النظام مُعد مسبقاً.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'fiscal_start_month' => 'required|integer|min:1|max:12',
            'default_currency_id' => 'required|exists:currencies,id',
            'decimal_places' => 'required|integer|min:0|max:6',
        ]);

        DB::beginTransaction();
        try {
            // معالجة رفع الشعار
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            // إنشاء إعدادات النظام
            $systemSetting = SystemSetting::create([
                'company_name' => $request->company_name,
                'logo' => $logoPath,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'fiscal_start_month' => $request->fiscal_start_month,
                'default_currency_id' => $request->default_currency_id,
                'decimal_places' => $request->decimal_places,
                'extra' => [
                    'setup_date' => now()->toDateString(),
                    'setup_by' => Auth::id() ?? 'system',
                    'version' => '1.0.0'
                ]
            ]);

            DB::commit();

            // تسجيل في سجل التدقيق
            AuditTrailController::log(
                'system_settings',
                $systemSetting->id,
                'I',
                'إعداد النظام الأولي - ' . $request->company_name,
                Auth::id() ?? 1
            );

            return redirect()->route('system-settings.index')
                ->with('success', 'تم إعداد النظام بنجاح! يمكنك الآن البدء في استخدام النظام.');
        } catch (\Exception $e) {
            DB::rollBack();
            // حذف الصورة إذا فشل الإنشاء
            if (isset($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            return back()->with('error', 'حدث خطأ أثناء إعداد النظام: ' . $e->getMessage());
        }
    }


    // نموذج تعديل
    public function edit($id)
    {
        $setting = SystemSetting::findOrFail($id);
        $currencies = Currency::all();
        return view('system-settings.edit', compact('setting', 'currencies'));
    }


    // تحديث بيانات النظام
    // تحديث بيانات النظام
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'fiscal_start_month' => 'required|integer|min:1|max:12',
            'default_currency_id' => 'nullable|exists:currencies,id',
            'decimal_places' => 'required|integer|min:0|max:6',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = SystemSetting::findOrFail($id);

        // معالجة رفع الشعار
        if ($request->hasFile('logo')) {
            // حذف الشعار القديم إذا موجود
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $setting->logo = $logoPath;
        }

        // تحديث باقي الحقول
        $setting->company_name = $request->company_name;
        $setting->email = $request->email;
        $setting->phone = $request->phone;
        $setting->address = $request->address;
        $setting->fiscal_start_month = $request->fiscal_start_month;
        $setting->default_currency_id = $request->default_currency_id;
        $setting->decimal_places = $request->decimal_places;

        $setting->save();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'system_settings',
            $setting->id,
            'U',
            'تحديث إعداد النظام - ' . $request->company_name,
            Auth::id()
        );
        return redirect()->route('system-settings.index')
            ->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }

    /**
     * التحقق من حالة النظام
     */
    public function checkSystemStatus()
    {
        $systemSettings = SystemSetting::first();
        $currenciesCount = Currency::count();

        return response()->json([
            'system_configured' => !is_null($systemSettings),
            'currencies_count' => $currenciesCount,
            'has_default_currency' => $systemSettings && $systemSettings->default_currency_id
        ]);
    }
}
