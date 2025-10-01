<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class SettingsController extends Controller
{
    public function edit()
    {
        // الحسابات (مجموعة) فقط صالحة كأب
        $groupAccounts = ChartOfAccount::where('is_group', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        // وكلاء
        $agentsAuto     = $this->getSettingBool('agents.auto_create_child_account', false);
        $agentsParentId = $this->getSettingInt('agents.parent_account_id');

        // موظفون
        $employeesAuto     = $this->getSettingBool('employees.auto_create_child_account', false);
        $employeesParentId = $this->getSettingInt('employees.parent_account_id');

        // بنوك
        $banksAuto     = $this->getSettingBool('banks.auto_create_child_account', false);
        $banksParentId = $this->getSettingInt('banks.parent_account_id');

        // صناديق
        $cashboxesAuto     = $this->getSettingBool('cashboxes.auto_create_child_account', false);
        $cashboxesParentId = $this->getSettingInt('cashboxes.parent_account_id');

        return view('settings.edit', compact(
            'groupAccounts',
            'agentsAuto',
            'agentsParentId',
            'employeesAuto',
            'employeesParentId',
            'banksAuto',
            'banksParentId',
            'cashboxesAuto',
            'cashboxesParentId'
        ));
    }
    /* ===== Helpers (مطابقة لطريقتك) ===== */
    private function getRow(string $name): ?Setting
    {
        return Setting::where('SettingName', $name)->first();
    }
    private function getBool(string $name, bool $default = false): bool
    {
        $row = $this->getRow($name);
        if (!$row || !$row->isActive) return $default;
        return in_array(strtolower((string)$row->SettingValue), ['1', 'true', 'yes', 'on'], true);
    }
    private function getInt(string $name, ?int $default = null): ?int
    {
        $row = $this->getRow($name);
        if (!$row || !$row->isActive || $row->SettingValue === '' || $row->SettingValue === null) return $default;
        return (int)$row->SettingValue;
    }
    public function update(Request $request) // للوكلاء
    {
        $request->validate([
            'auto_create' => 'nullable|boolean',
            'parent_id'   => 'nullable|exists:chart_of_accounts,id',
        ], ['parent_id.exists' => 'الحساب الأب غير موجود']);

        $this->setSetting('agents.auto_create_child_account', $request->boolean('auto_create'), 'bool', true);

        if ($request->filled('parent_id')) {
            $this->setSetting('agents.parent_account_id', (int)$request->parent_id, 'int', true);
        } else {
            $this->setSetting('agents.parent_account_id', '', 'int', true);
        }
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'settings',
            0,
            'U',
            'تعديل إعدادات حسابات الوكيل: الية انشاء الحساب التلقائي - تعديل رقم حساب مجموعة التي يندرج تحتها الوكلاء',
            Auth::id()
        );

        return redirect()->route('settings.edit', ['tab' => 'agents'])
            ->with('success', 'تم حفظ إعدادات الوكلاء بنجاح');
    }

    public function updateEmployee(Request $request) // للموظفين
    {
        $request->validate([
            'auto_create' => 'nullable|boolean',
            'parent_id'   => 'nullable|exists:chart_of_accounts,id',
        ], ['parent_id.exists' => 'الحساب الأب غير موجود']);

        $this->setSetting('employees.auto_create_child_account', $request->boolean('auto_create'), 'bool', true);

        if ($request->filled('parent_id')) {
            $this->setSetting('employees.parent_account_id', (int)$request->parent_id, 'int', true);
        } else {
            $this->setSetting('employees.parent_account_id', '', 'int', true);
        }
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'settings',
            0,
            'U',
            'تعديل إعدادات حسابات الموظفين: الية انشاء الحساب التلقائي - تعديل رقم حساب مجموعة التي يندرج تحتها الموظفين',
            Auth::id()
        );
        return redirect()->route('settings.edit', ['tab' => 'employees'])
            ->with('success', 'تم حفظ إعدادات الموظفين بنجاح');
    }
    /* ----------------- بنوك ----------------- */
    public function updateBanks(Request $request)
    {
        $request->validate([
            'auto_create' => 'nullable|boolean',
            'parent_id'   => 'nullable|exists:chart_of_accounts,id',
        ], ['parent_id.exists' => 'الحساب الأب غير موجود']);

        $this->setSetting('banks.auto_create_child_account', $request->boolean('auto_create'), 'bool', true);
        if ($request->filled('parent_id')) {
            $this->setSetting('banks.parent_account_id', (int)$request->parent_id, 'int', true);
        } else {
            $this->setSetting('banks.parent_account_id', '', 'int', true);
        }
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'settings',
            0,
            'U',
            'تعديل إعدادات حسابات البنوك: الية انشاء الحساب التلقائي - تعديل رقم حساب مجموعة التي يندرج تحتها البنوك',
            Auth::id()
        );
        return redirect()->route('settings.edit', ['tab' => 'banks'])
            ->with('success', 'تم حفظ إعدادات البنوك بنجاح');
    }

    /* ----------------- صناديق ----------------- */
    public function updateCashboxes(Request $request)
    {
        $request->validate([
            'auto_create' => 'nullable|boolean',
            'parent_id'   => 'nullable|exists:chart_of_accounts,id',
        ], ['parent_id.exists' => 'الحساب الأب غير موجود']);

        $this->setSetting('cashboxes.auto_create_child_account', $request->boolean('auto_create'), 'bool', true);
        if ($request->filled('parent_id')) {
            $this->setSetting('cashboxes.parent_account_id', (int)$request->parent_id, 'int', true);
        } else {
            $this->setSetting('cashboxes.parent_account_id', '', 'int', true);
        }
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'settings',
            0,
            'U',
            'تعديل إعدادات حسابات الصناديق: الية انشاء الحساب التلقائي - تعديل رقم حساب مجموعة التي يندرج تحتها الصناديق',
            Auth::id()
        );
        return redirect()->route('settings.edit', ['tab' => 'cashboxes'])
            ->with('success', 'تم حفظ إعدادات الصناديق بنجاح');
    }

    /* -------------------- Helpers محلية على نفس الكنترولر -------------------- */
    private function getSettingRow(string $name): ?Setting
    {
        return Setting::where('SettingName', $name)->first();
    }

    private function getSettingBool(string $name, bool $default = false): bool
    {
        $row = $this->getSettingRow($name);
        if (!$row || !$row->isActive) return $default;
        return in_array(strtolower((string)$row->SettingValue), ['1', 'true', 'yes', 'on'], true);
    }

    private function getSettingInt(string $name, ?int $default = null): ?int
    {
        $row = $this->getSettingRow($name);
        if (!$row || !$row->isActive || $row->SettingValue === '' || $row->SettingValue === null) return $default;
        return (int)$row->SettingValue;
    }

    private function setSetting(string $name, $value, string $type = 'string', bool $active = true): void
    {
        Setting::updateOrCreate(
            ['SettingName' => $name],
            [
                'SettingValue' => is_bool($value) ? ($value ? '1' : '0') : (string)$value,
                'DataType'     => $type,
                'isActive'     => $active,
            ]
        );
    }
}
