<?php

namespace App\Imports;

use App\Models\ChartOfAccount;
use App\Models\AccountType;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ChartOfAccountsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // ==== 0) تطبيع المدخلات ====
        $code = isset($row['code']) ? trim((string)$row['code']) : '';
        if ($code === '') {
            // لا يمكن إنشاء حساب بدون كود
            return null;
        }

        // is_group: 1/true أو 0/false
        $isGroup = false;
        if (isset($row['is_group'])) {
            $val = strtolower(trim((string)$row['is_group']));
            $isGroup = ($val === '1' || $val === 'true' || $val === 'yes' || $val === 'on');
        }

        // nature: debit/مدين (افتراضي) أو credit/دائن
        $nature = 'debit';
        if (!empty($row['nature'])) {
            $n = strtolower(trim((string)$row['nature']));
            if (in_array($n, ['credit', 'دائن'], true)) {
                $nature = 'credit';
            } elseif (in_array($n, ['debit', 'مدين'], true)) {
                $nature = 'debit';
            }
        }

        // الاسم/الوصف
        $name        = isset($row['name']) ? trim((string)$row['name']) : null;
        $description = isset($row['description']) ? trim((string)$row['description']) : null;

        // العملة
        $currencyId = isset($row['currency_id']) && $row['currency_id'] !== ''
            ? (int)$row['currency_id']
            : null;

        // الحالة
        $status = isset($row['status']) && $row['status'] !== ''
            ? trim((string)$row['status'])
            : 'نشط';

        // allow_entry & is_default منطقياً يتبعان is_group
        $allowEntry = $isGroup ? 0 : 1;

        $isDefault = 0;
        if (isset($row['is_default'])) {
            $v = strtolower(trim((string)$row['is_default']));
            $isDefault = ($v === '1' || $v === 'true' || $v === 'yes' || $v === 'on') ? 1 : 0;
        }

        // ==== 1) إيجاد الأب parent_id بالكود (إن وُجد) ====
        $parentId = null;
        if (!empty($row['parent_code'])) {
            $parentCode = trim((string)$row['parent_code']);
            // نبحث حتى لو الأب محذوف soft (وغالباً يجب أن لا يكون محذوفاً، لكن لنمنع الفشل)
            $parent = ChartOfAccount::withTrashed()->where('code', $parentCode)->first();
            if ($parent) {
                $parentId = $parent->id;
            }
        }

        // ==== 2) إيجاد نوع الحساب (بالاسم أو الـID) ====
        $accountTypeId = null;
        if (!empty($row['account_type']) && !$isGroup) {
            $at = trim((string)$row['account_type']);

            // إن كانت قيمة رقمية نعتبرها ID، وإلا نحاول بالاسم
            if (ctype_digit($at)) {
                $accType = AccountType::find((int)$at);
            } else {
                $accType = AccountType::where('name', $at)->first();
            }

            if ($accType) {
                $accountTypeId = $accType->id;
            }
        }

        // ==== 3) نبني بيانات السجل ====
        $data = [
            'parent_id'       => $parentId,
            'name'            => $name,
            'description'     => $description,
            'account_type_id' => $isGroup ? null : $accountTypeId,
            'nature'          => $nature,
            'is_group'        => $isGroup,
            'level'           => $parentId ? $this->calculateLevel($parentId) : 1,
            'currency_id'     => $currencyId,
            'allow_entry'     => $allowEntry,
            'is_default'      => $isDefault,
            'status'          => $status,
        ];

        // ==== 4) معالجة الـ unique مع السجلات المحذوفة soft ====
        // إذا كان هناك سجل بنفس الكود لكنه محذوف soft، نعيده (restore) ثم نحدثه.
        return DB::transaction(function () use ($code, $data) {
            $existing = ChartOfAccount::withTrashed()->where('code', $code)->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->fill($data)->save();
                return $existing;
            }

            // إنشاء جديد
            return ChartOfAccount::create(array_merge($data, ['code' => $code]));
        });
    }

    /**
     * حساب المستوى (Level) حسب الحساب الأب.
     */
    private function calculateLevel($parentId)
    {
        $parent = ChartOfAccount::withTrashed()->find($parentId);
        return $parent ? ((int)$parent->level + 1) : 1;
    }
}
