<?php
// app/Http/Controllers/DocumentSequenceController.php
namespace App\Http\Controllers;

use App\Models\DocumentSequence;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ← إضافة هذا

class DocumentSequenceController extends Controller
{
    public function index(Request $request)
    {
        $q = (string)$request->get('q', '');
        $sequences = DocumentSequence::with('type')
            ->when($q, fn($s) => $s->where(function ($w) use ($q) {
                $w->where('prefix', 'like', "%$q%")
                    ->orWhereHas('type', fn($t) => $t->where('code', 'like', "%$q%")->orWhere('name', 'like', "%$q%"));
            }))
            ->orderByDesc('id')
            ->paginate(15);

        $stats = [
            'total' => DocumentSequence::count(),
            'active' => DocumentSequence::where('is_active', true)->count(),
            'per_year' => DocumentSequence::where('reset_period', 'year')->count(),
            'per_month' => DocumentSequence::where('reset_period', 'month')->count(),
        ];

        return view('document_sequences.index', compact('sequences', 'stats', 'q'));
    }

    public function create()
    {
        $types = DocumentType::orderBy('code')->get();
        return view('document_sequences.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'branch_id' => 'nullable|integer',
            'fiscal_year_id' => 'nullable|integer',
            'prefix' => 'nullable|string|max:20',
            'start_number' => 'required|integer|min:1',
            'current_number' => 'nullable|integer|min:0',
            'padding' => 'required|integer|min:1|max:10',
            'reset_period' => 'required|in:none,year,month',
            'is_active' => 'nullable|boolean',
        ]);
        $data['current_number'] = $data['current_number'] ?? 0;
        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $document_sequence = DocumentSequence::create($data);
        //add  تسجيل في سجل التدقيق
        AuditTrailController::log(
            'document_sequences',
            $document_sequence->id,
            'I',
            'أضافة تسلسل جديد للوثيقة: ' . $document_sequence->type->name,
            Auth::id()
        );
        return redirect()->route('document-sequences.index')->with('success', 'تم إضافة تسلسل وثائق');
    }

    public function edit(DocumentSequence $document_sequence)
    {
        $types = DocumentType::orderBy('code')->get();
        return view('document_sequences.edit', ['sequence' => $document_sequence, 'types' => $types]);
    }

    public function update(Request $request, DocumentSequence $document_sequence)
    {
        $data = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'branch_id' => 'nullable|integer',
            'fiscal_year_id' => 'nullable|integer',
            'prefix' => 'nullable|string|max:20',
            'start_number' => 'required|integer|min:1',
            'current_number' => 'nullable|integer|min:0',
            'padding' => 'required|integer|min:1|max:10',
            'reset_period' => 'required|in:none,year,month',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $document_sequence->update($data);
        //edit تسجيل في سجل التدقيق
        AuditTrailController::log(
            'document_sequences',
            $document_sequence->id,
            'U',
            'تعديل بيانات تسلسل وثيقة: ' . $document_sequence->type->name,
            Auth::id()
        );
        return redirect()->route('document-sequences.index')->with('success', 'تم تحديث التسلسل');
    }

    public function destroy(DocumentSequence $document_sequence)
    {
        $document_sequence->delete();
        //delete تسجيل في سجل التدقيق
        AuditTrailController::log(
            'document_sequences',
            $document_sequence->id,
            'D',
            'حذف بيانات تسلسل وثيقة: ' . $document_sequence->type->name,
            Auth::id()
        );
        return redirect()->route('document-sequences.index')->with('success', 'تم حذف التسلسل');
    }
}
