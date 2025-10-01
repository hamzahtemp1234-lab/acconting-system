<?php
// app/Http/Controllers/DocumentTypeController.php
namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا


class DocumentTypeController extends Controller
{
    public function index(Request $request)
    {
        $q = (string)$request->get('q', '');
        $types = DocumentType::when($q, fn($s) => $s->where(function ($w) use ($q) {
            $w->where('code', 'like', "%$q%")
                ->orWhere('name', 'like', "%$q%")
                ->orWhere('module', 'like', "%$q%");
        }))
            ->orderBy('code')
            ->paginate(15);

        $stats = [
            'total' => DocumentType::count(),
            'active' => DocumentType::where('is_active', true)->count(),
            'accounting' => DocumentType::where('module', 'accounting')->count(),
            'need_approval' => DocumentType::where('requires_approval', true)->count(),
        ];

        return view('document_types.index', compact('types', 'stats', 'q'));
    }

    public function create()
    {
        return view('document_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:document_types,code',
            'name' => 'required|string|max:200',
            'module' => 'required|in:accounting,sales,purchases,general',
            'is_active' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['requires_approval'] = (bool)($data['requires_approval'] ?? false);

        $document_type = DocumentType::create($data);
        //add  تسجيل في سجل التدقيق
        AuditTrailController::log(
            'document_types',
            $document_type->id,
            'I',
            'أضافة نوع وثيقه جديد: ' . $document_type->name,
            Auth::id()
        );
        return redirect()->route('document-types.index')->with('success', 'تم إضافة نوع الوثيقة');
    }

    public function edit(DocumentType $document_type)
    {
        return view('document_types.edit', ['type' => $document_type]);
    }

    public function update(Request $request, DocumentType $document_type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'module' => 'required|in:accounting,sales,purchases,general',
            'is_active' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['requires_approval'] = (bool)($data['requires_approval'] ?? false);

        $document_type->update($data);
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'document_types',
            $document_type->id,
            'U',
            'تعديل بيانات نوع الوثيقة: ' . $document_type->name,
            Auth::id()
        );
        return redirect()->route('document-types.index')->with('success', 'تم تحديث نوع الوثيقة');
    }

    public function destroy(DocumentType $document_type)
    {
        $document_type->delete();
        //delete تسجيل في سجل التدقيق
        AuditTrailController::log(
            'document_types',
            $document_type->id,
            'D',
            'حذف بيانات نوع الوثيقة: ' . $document_type->name,
            Auth::id()
        );
        return redirect()->route('document-types.index')->with('success', 'تم حذف نوع الوثيقة');
    }
}
