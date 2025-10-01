<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * عرض سجل النشاطات
     */
    public function index(Request $request)
    {
        $query = Log::with('user')->orderBy('Timestamp', 'desc');

        // البحث في الرسائل
        if ($request->has('search') && $request->search != '') {
            $query->where('Message', 'like', "%{$request->search}%");
        }

        // التصفية حسب المستوى
        if ($request->has('level') && $request->level != '') {
            $query->where('LogLevel', $request->level);
        }

        // التصفية حسب المستخدم
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('UserID', $request->user_id);
        }

        // التصفية حسب التاريخ
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('Timestamp', $request->date);
        }

        $logs = $query->paginate(20);
        $users = User::where('IsActive', true)->get();
        $logLevels = ['INFO', 'WARNING', 'ERROR', 'DEBUG'];

        return view('logs.index', compact('logs', 'users', 'logLevels'));
    }

    /**
     * عرض تفاصيل سجل معين
     */
    public function show(Log $log)
    {
        $log->load('user');
        return view('logs.show', compact('log'));
    }

    /**
     * حذف سجلات قديمة
     */
    public function clear(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = now()->subDays($validated['days']);

        $deletedCount = Log::where('Timestamp', '<', $cutoffDate)->delete();

        // تسجيل عملية المسح
        AuditTrailController::log('logs', null, 'D', "مسح {$deletedCount} سجل نشاط أقدم من {$validated['days']} يوم", Auth::id());

        return back()->with('success', "تم مسح {$deletedCount} سجل نشاط بنجاح.");
    }

    /**
     * تصدير السجلات
     */
    public function export(Request $request)
    {
        $query = Log::with('user')->orderBy('Timestamp', 'desc');

        // تطبيق نفس عوامل التصفية
        if ($request->has('level') && $request->level != '') {
            $query->where('LogLevel', $request->level);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('UserID', $request->user_id);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('Timestamp', $request->date);
        }

        $logs = $query->get();

        // إنشاء ملف CSV
        $fileName = 'logs_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['التاريخ والوقت', 'المستوى', 'المستخدم', 'الرسالة'], ',');

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->Timestamp->format('Y-m-d H:i:s'),
                    $log->LogLevel,
                    $log->user ? $log->user->name : 'System',
                    $log->Message
                ], ',');
            }
            fclose($file);
        };

        // تسجيل عملية التصدير
        AuditTrailController::log('logs', null, 'R', 'تصدير سجلات النشاطات', Auth::id());

        return response()->stream($callback, 200, $headers);
    }

    /**
     * إحصائيات السجلات
     */
    public function statistics()
    {
        $stats = [
            'total' => Log::count(),
            'today' => Log::whereDate('Timestamp', today())->count(),
            'errors' => Log::where('LogLevel', 'ERROR')->count(),
            'warnings' => Log::where('LogLevel', 'WARNING')->count(),
        ];

        return response()->json($stats);
    }
}
