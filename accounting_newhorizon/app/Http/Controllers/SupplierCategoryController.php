<?php

namespace App\Http\Controllers;

use App\Models\SupplierCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class SupplierCategoryController extends Controller
{
    public function index(Request $request)
    {
        $qText = $request->get('q', '');

        $query = \App\Models\SupplierCategory::withTrashed();

        if ($qText !== '') {
            $query->where(function ($w) use ($qText) {
                $w->where('code', 'like', "%{$qText}%")
                    ->orWhere('name', 'like', "%{$qText}%")
                    ->orWhere('description', 'like', "%{$qText}%");
            });
        }

        // <-- المهم: استخدم paginate بدل get
        $categories = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('supplier_categories.index', compact('categories', 'qText'));
    }


    public function create()
    {
        // جلب الحسابات من الدليل المحاسبي للربط الاختياري
        $accounts = \App\Models\ChartOfAccount::orderBy('code')->get();
        $nextCode = SupplierCategory::nextCode();
        return view('supplier_categories.create', compact('nextCode', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|unique:suplier_categories,code|max:20',
            'name'        => 'required|string|max:255',
            'is_active'   => 'boolean',
            // الربط الاختياري مع الدليل المحاسبي:
            'account_id'  => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
        ]);

        // إنشاء السجل (نفس أسلوبك في العملاء)
        $supplierCategory = SupplierCategory::create($request->all());
        //add  تسجيل في سجل التدقيق
        AuditTrailController::log(
            'suplier_categories',
            $supplierCategory->id,
            'I',
            'أضافة تصنيف موردين جديد: ' . $supplierCategory->name,
            Auth::id()
        );
        return redirect()
            ->route('supplier-categories.index')
            ->with('success', 'تم إضافة تصنيف المورد بنجاح');
    }

    public function edit(SupplierCategory $supplierCategory)
    {
        $accounts = \App\Models\ChartOfAccount::orderBy('code')->get();
        $category = $supplierCategory; // لتوحيد اسم المتغير في الواجهة
        return view('supplier_categories.edit', compact('category', 'accounts'));
        // أو: return view('supplier_categories.edit', ['category' => $supplierCategory, 'accounts' => $accounts]);
    }

    public function update(Request $request, SupplierCategory $supplierCategory)
    {
        $validated = $request->validate([
            //'code'        => 'required|max:20|unique:supplier_categories,code,' . $supplierCategory->id,
            'name'        => 'required|string|max:255',
            'is_active'   => 'required|boolean',
            'account_id'  => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
        ]);

        $supplierCategory->update($validated);
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'suplier_categories',
            $supplierCategory->id,
            'U',
            'تعديل بيانات تصنيف الموردين: ' . $supplierCategory->name,
            Auth::id()
        );
        return redirect()
            ->route('supplier-categories.index')
            ->with('success', 'تم تحديث بيانات تصنيف المورد');
    }

    public function destroy(SupplierCategory $supplierCategory)
    {
        $supplierCategory->delete();
        //delete تسجيل في سجل التدقيق
        AuditTrailController::log(
            'suplier_categories',
            $supplierCategory->id,
            'D',
            'حذف بيانات تصنيف الموردين: ' . $supplierCategory->name,
            Auth::id()
        );
        return redirect()
            ->route('supplier-categories.index')
            ->with('success', 'تم حذف تصنيف المورد');
    }
}
