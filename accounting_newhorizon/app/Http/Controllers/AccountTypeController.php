<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountType;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا
class AccountTypeController extends Controller
{
    public function index()
    {
        $types = AccountType::all();
        return view('account-types.index', compact('types'));
    }

    public function create()
    {
        return view('account-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nature' => 'required|in:debit,credit',
        ]);

        $typeAccount = AccountType::create($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'account_types',
            $typeAccount->id,
            'I',
            'إنشاء نوع حساب جديد: ' . $typeAccount->name,
            Auth::id()
        );
        return redirect()->route('account-types.index')
            ->with('success', 'تم إضافة نوع الحساب بنجاح');
    }

    public function edit($id)
    {
        $type = AccountType::findOrFail($id);
        return view('account-types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = AccountType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nature' => 'required|in:debit,credit',
        ]);

        $type->update($request->all());
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'account_types',
            $type->id,
            'U',
            'تعديل نوع حساب : ' . $type->name,
            Auth::id()
        );
        return redirect()->route('account-types.index')
            ->with('success', 'تم تحديث نوع الحساب بنجاح');
    }

    public function destroy($id)
    {
        $type = AccountType::findOrFail($id);
        $type->delete();
        // تسجيل في سجل التدقيق
        AuditTrailController::log(
            'account_types',
            $type->id,
            'D',
            'حذف نوع حساب : ' . $type->name,
            Auth::id()
        );
        return redirect()->route('account-types.index')
            ->with('success', 'تم حذف نوع الحساب بنجاح');
    }
}
