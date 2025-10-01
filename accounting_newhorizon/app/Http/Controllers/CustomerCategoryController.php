<?php

namespace App\Http\Controllers;

use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class CustomerCategoryController extends Controller
{
    public function index()
    {
        $categories = CustomerCategory::withTrashed()->get();
        return view('customer_categories.index', compact('categories'));
    }

    public function create()
    {
        $accounts = \App\Models\ChartOfAccount::orderBy('code')->get();
        return view('customer_categories.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customer_categories,code|max:20',
            'name' => 'required|string|max:255',
            'isActive' => 'boolean',
            // ضمن قواعد التحقق:
            'account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $customerCategory = CustomerCategory::create($request->all());
        //add  تسجيل في سجل التدقيق
        AuditTrailController::log(
            'customer_categories',
            $customerCategory->id,
            'I',
            'أضافة تصنيف عملاء جديد: ' . $customerCategory->name,
            Auth::id()
        );
        return redirect()->route('customer-categories.index')->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit(CustomerCategory $customerCategory)
    {
        $accounts = \App\Models\ChartOfAccount::orderBy('code')->get();
        $category = $customerCategory;
        return view('customer_categories.edit', compact('category', 'accounts'));
        //return view('customer_categories.edit', ['category' => $customerCategory]);
    }
    public function update(Request $request, CustomerCategory $customerCategory)
    {
        $validated = $request->validate([
            'code' => 'required|max:20|unique:customer_categories,code,' . $customerCategory->id,
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            // ضمن قواعد التحقق:
            'account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $customerCategory->update($validated);
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'customer_categories',
            $customerCategory->id,
            'U',
            'تعديل بيانات تصنيف العملاء: ' . $customerCategory->name,
            Auth::id()
        );
        //dd($request->all());
        return redirect()
            ->route('customer-categories.index')
            ->with('success', 'تم تحديث بيانات التصنيف');
    }



    public function destroy(CustomerCategory $customerCategory)
    {
        $customerCategory->delete();
        //delete تسجيل في سجل التدقيق
        AuditTrailController::log(
            'customer_categories',
            $customerCategory->id,
            'D',
            'حذف بيانات تصنيف العملاء: ' . $customerCategory->name,
            Auth::id()
        );
        return redirect()->route('customer-categories.index')->with('success', 'تم حذف التصنيف');
    }
}
