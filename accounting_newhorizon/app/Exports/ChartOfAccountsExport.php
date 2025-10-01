<?php

namespace App\Exports;

use App\Models\ChartOfAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChartOfAccountsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ChartOfAccount::with(['parent', 'accountType'])
            ->get()
            ->map(function ($account) {
                return [
                    'code'         => $account->code,
                    'name'         => $account->name,
                    'parent_code'  => $account->parent ? $account->parent->code : null, // ✅ كود الأب بدل ID
                    'account_type' => $account->accountType ? $account->accountType->name : null, // ✅ اسم النوع بدل ID
                    'nature'       => $account->nature === 'credit' ? 'دائن' : 'مدين', // ✅ بالعربي أو ممكن تخليه بالإنجليزي
                    'is_group'     => $account->is_group ? 1 : 0,
                    'description'  => $account->description,
                    'currency_id'  => $account->currency_id,
                    'is_default'   => $account->is_default,
                    'status'       => $account->status,
                ];
            });
    }

    // ✅ نفس العناوين المستخدمة في الاستيراد
    public function headings(): array
    {
        return [
            'code',
            'name',
            'parent_code',
            'account_type',
            'nature',
            'is_group',
            'description',
            'currency_id',
            'is_default',
            'status',
        ];
    }
}
