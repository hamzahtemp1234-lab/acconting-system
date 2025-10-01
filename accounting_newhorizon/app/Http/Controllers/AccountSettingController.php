<?php

namespace App\Http\Controllers;

use App\Models\AccountSetting;
use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountSettingController extends Controller
{
    public function index(Request $request)
    {
        $module = $request->get('module');
        $q      = $request->get('q');

        $settings = AccountSetting::with('account')
            ->when($module, fn($qq) => $qq->where('module', $module))
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('key', 'like', "%$q%")
                        ->orWhere('notes', 'like', "%$q%");
                });
            })
            ->orderBy('module')
            ->orderBy('key')
            ->paginate(20);

        $modules = array_keys(AccountSetting::MODULE_KEYS);

        return view('account_settings.index', compact('settings', 'module', 'modules', 'q'));
    }

    public function create()
    {
        $modules = AccountSetting::MODULE_KEYS; // module => keys[]
        $accounts = ChartOfAccount::orderBy('code')->get();
        $currencies = Currency::orderBy('code')->get();
        $customerCategories = CustomerCategory::orderBy('name')->get();

        return view('account_settings.create', compact('modules', 'accounts', 'currencies', 'customerCategories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        // منع التكرار لنفس النطاق
        $this->uniqueGuard($validated);

        AccountSetting::create($validated);

        return redirect()->route('account-settings.index')->with('success', 'تم إضافة إعداد الحساب بنجاح');
    }

    public function edit(AccountSetting $accountSetting)
    {
        $modules = AccountSetting::MODULE_KEYS;
        $accounts = ChartOfAccount::orderBy('code')->get();
        $currencies = Currency::orderBy('code')->get();
        $customerCategories = CustomerCategory::orderBy('name')->get();

        return view('account_settings.edit', [
            'setting' => $accountSetting,
            'modules' => $modules,
            'accounts' => $accounts,
            'currencies' => $currencies,
            'customerCategories' => $customerCategories,
        ]);
    }

    public function update(Request $request, AccountSetting $accountSetting)
    {
        $validated = $this->validatePayload($request, $accountSetting->id);

        // منع التكرار لنفس النطاق
        $this->uniqueGuard($validated, $accountSetting->id);

        $accountSetting->update($validated);

        return redirect()->route('account-settings.index')->with('success', 'تم تحديث إعداد الحساب');
    }

    public function destroy(AccountSetting $accountSetting)
    {
        $accountSetting->delete();
        return redirect()->route('account-settings.index')->with('success', 'تم حذف الإعداد');
    }

    /* ================= Helpers ================= */

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $modules = array_keys(AccountSetting::MODULE_KEYS);
        $allKeys = AccountSetting::MODULE_KEYS; // module => keys[]

        $rules = [
            'module' => ['required', Rule::in($modules)],
            'key'    => ['required'],
            'account_id'   => ['nullable', 'exists:chart_of_accounts,id'],
            'value_string' => ['nullable', 'string', 'max:255'],
            'is_active'    => ['required', 'boolean'],

            'scope_type' => ['nullable', Rule::in(AccountSetting::SCOPES)],
            'scope_id'   => ['nullable', 'integer'],
            'notes'      => ['nullable', 'string'],
        ];

        $messages = [
            'module.required' => 'حقل الوحدة مطلوب',
            'module.in'       => 'قيمة الوحدة غير صحيحة',
            'key.required'    => 'حقل المفتاح مطلوب',
            'account_id.exists' => 'الحساب غير موجود',
            'is_active.required' => 'الحالة مطلوبة',
            'is_active.boolean'  => 'الحالة يجب أن تكون (1 أو 0)',
            'scope_type.in'    => 'نطاق غير صحيح',
        ];

        $data = $request->validate($rules, $messages);

        // تحقق أن المفتاح ضمن مفاتيح الوحدة
        if (!in_array($data['key'], $allKeys[$data['module']] ?? [])) {
            abort(422, 'المفتاح المختار غير متاح لهذه الوحدة');
        }

        // تأكيد تطابق scope_id مع نوعه
        if (!empty($data['scope_type']) && !empty($data['scope_id'])) {
            if ($data['scope_type'] === 'currency' && !Currency::where('id', $data['scope_id'])->exists()) {
                abort(422, 'العملة المحددة غير موجودة');
            }
            if ($data['scope_type'] === 'customer_category' && !CustomerCategory::where('id', $data['scope_id'])->exists()) {
                abort(422, 'تصنيف العملاء المحدد غير موجود');
            }
        } else {
            // لو لا يوجد نطاق → اجعله عام
            $data['scope_type'] = null;
            $data['scope_id']   = null;
        }

        // إن لم يُرسل account_id و أردت السماح بإعداد نصّي فقط
        // اتركه كما هو؛ معظم المفاتيح حسابات لذلك account_id محبّذ.

        return $data;
    }

    private function uniqueGuard(array $data, ?int $ignoreId = null): void
    {
        $exists = AccountSetting::when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('module', $data['module'])
            ->where('key', $data['key'])
            ->where(function ($q) use ($data) {
                if (is_null($data['scope_type'])) {
                    $q->whereNull('scope_type')->whereNull('scope_id');
                } else {
                    $q->where('scope_type', $data['scope_type'])
                        ->where('scope_id', $data['scope_id']);
                }
            })
            ->exists();

        if ($exists) {
            abort(422, 'يوجد إعداد بنفس الوحدة والمفتاح والنطاق بالفعل.');
        }
    }
}
