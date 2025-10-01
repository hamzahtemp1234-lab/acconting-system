<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditTrailController extends Controller
{

    /**
     * عرض قائمة مسارات التدقيق
     */
    public function index(Request $request)
    {
        $query = AuditTrail::with('changedByUser')
            ->orderBy('ChangeDate', 'desc');

        // ... كود الفلاتر الحالي ...

        $auditTrails = $query->paginate(20);

        // الجداول المتاحة للتصفية
        $tables = AuditTrail::distinct()->pluck('TableName');

        // المستخدمون المتاحون للتصفية
        $users = User::where('IsActive', true)->get();

        // إحصائيات للإرسال إلى الـ view
        $statistics = [
            'total' => $auditTrails->total(),
            'inserts' => AuditTrail::where('ChangeType', 'I')->count(),
            'updates' => AuditTrail::where('ChangeType', 'U')->count(),
            'deletes' => AuditTrail::where('ChangeType', 'D')->count(),
        ];

        return view('audit-trails.index', compact('auditTrails', 'tables', 'users', 'statistics'));
    }
    // public function index(Request $request)
    // {

    //     $query = AuditTrail::with('changedByUser')
    //         ->orderBy('ChangeDate', 'desc');

    //     // البحث
    //     if ($request->has('search') && $request->search != '') {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('TableName', 'like', "%{$search}%")
    //                 ->orWhere('Details', 'like', "%{$search}%")
    //                 ->orWhereHas('changedByUser', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%")
    //                         ->orWhere('email', 'like', "%{$search}%");
    //                 });
    //         });
    //     }

    //     // التصفية حسب الجدول
    //     if ($request->has('table') && $request->table != '') {
    //         $query->where('TableName', $request->table);
    //     }

    //     // التصفية حسب نوع التغيير
    //     if ($request->has('change_type') && $request->change_type != '') {
    //         $query->where('ChangeType', $request->change_type);
    //     }

    //     // التصفية حسب المستخدم
    //     if ($request->has('user_id') && $request->user_id != '') {
    //         $query->where('ChangedBy', $request->user_id);
    //     }

    //     // التصفية حسب التاريخ
    //     if ($request->has('date_from') && $request->date_from != '') {
    //         $query->whereDate('ChangeDate', '>=', $request->date_from);
    //     }

    //     if ($request->has('date_to') && $request->date_to != '') {
    //         $query->whereDate('ChangeDate', '<=', $request->date_to);
    //     }

    //     $auditTrails = $query->paginate(20);

    //     // الجداول المتاحة للتصفية
    //     $tables = AuditTrail::distinct()->pluck('TableName');

    //     // المستخدمون المتاحون للتصفية
    //     $users = User::where('IsActive', true)->get();

    //     return view('audit-trails.index', compact('auditTrails', 'tables', 'users'));
    // }

    /**
     * عرض تفاصيل مسار التدقيق
     */
    public function show(AuditTrail $auditTrail)
    {
        $auditTrail->load('changedByUser');

        return view('audit-trails.show', compact('auditTrail'));
    }

    /**
     * حذف مسار التدقيق
     */
    public function destroy(AuditTrail $auditTrail)
    {
        DB::beginTransaction();
        try {
            // تسجيل قبل الحذف
            \App\Http\Controllers\AuditTrailController::log(
                'audit_trails',
                $auditTrail->id,
                'D',
                'حذف سجل التدقيق: ' . $auditTrail->TableName . ' - ' . $auditTrail->ChangeDate,
                Auth::id()
            );

            $auditTrail->delete();

            DB::commit();

            return redirect()->route('audit-trails.index')
                ->with('success', 'تم حذف سجل التدقيق بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف سجل التدقيق: ' . $e->getMessage());
        }
    }

    /**
     * تصدير مسارات التدقيق
     */
    public function export(Request $request)
    {
        $query = AuditTrail::with('changedByUser')
            ->orderBy('ChangeDate', 'desc');

        // تطبيق الفلاتر إذا وجدت
        if ($request->has('table') && $request->table != '') {
            $query->where('TableName', $request->table);
        }

        if ($request->has('change_type') && $request->change_type != '') {
            $query->where('ChangeType', $request->change_type);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('ChangeDate', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('ChangeDate', '<=', $request->date_to);
        }

        $auditTrails = $query->get();

        // هنا يمكنك إضافة منطق التصدير لـ Excel أو PDF
        // سأعرض مثالاً بسيطاً للتصدير كـ CSV

        $fileName = 'audit_trails_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($auditTrails) {
            $file = fopen('php://output', 'w');

            // كتابة العنوان العربي مع BOM للتصحيح
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'التاريخ والوقت',
                'الجدول',
                'معرف السجل',
                'نوع التغيير',
                'المستخدم',
                'التفاصيل'
            ]);

            foreach ($auditTrails as $trail) {
                fputcsv($file, [
                    $trail->ChangeDate->format('Y-m-d H:i:s'),
                    $trail->TableName,
                    $trail->RecordID,
                    $this->getChangeTypeArabic($trail->ChangeType),
                    $trail->changedByUser ? $trail->changedByUser->name : 'غير معروف',
                    $trail->Details
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * الحصول على نوع التغيير بالعربية
     */
    private function getChangeTypeArabic($changeType)
    {
        $types = [
            'I' => 'إضافة',
            'U' => 'تعديل',
            'D' => 'حذف'
        ];

        return $types[$changeType] ?? $changeType;
    }

    /**
     * تنظيف السجلات القديمة
     */
    /**
     * تنظيف السجلات القديمة
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->days;
        $cutoffDate = now()->subDays($days);

        DB::beginTransaction();
        try {
            $deletedCount = AuditTrail::where('ChangeDate', '<', $cutoffDate)->delete();

            DB::commit();

            // تسجيل عملية التنظيف
            self::log(
                'audit_trails',
                0,
                'D',
                'تنظيف سجلات التدقيق الأقدم من ' . $days . ' يوم. تم حذف ' . $deletedCount . ' سجل.',
                Auth::id()
            );

            return redirect()->route('audit-trails.index')
                ->with('success', 'تم تنظيف ' . $deletedCount . ' سجل تدقيق بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تنظيف السجلات: ' . $e->getMessage());
        }
    }
    /**
     * تسجيل عملية في جدول مسارات التدقيق
     */
    public static function log($tableName, $recordId, $changeType, $details, $changedBy)
    {
        \App\Models\AuditTrail::create([
            'TableName'   => $tableName,
            'RecordID'    => $recordId,
            'ChangeType'  => $changeType,
            'Details'     => $details,
            'ChangedBy'   => $changedBy,
            'ChangeDate'  => now(),
        ]);
    }
}
