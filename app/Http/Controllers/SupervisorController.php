<?php

namespace App\Http\Controllers;

use App\Exports\SupervisorsTemplateExport;
use App\Http\Controllers\Concerns\HandlesExcelImport;
use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\StoreSupervisorRequest;
use App\Http\Requests\UpdateSupervisorRequest;
use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Services\SupervisorImportService;
use App\Support\ClassAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupervisorController extends Controller
{
    use HandlesExcelImport;
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filters = $request->only(['search', 'school_class_id', 'status', 'warnings']);

        $supervisors = Supervisor::query()
            ->with('schoolClass')
            ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('school_class_id'), fn ($q) => $q->where('school_class_id', $request->school_class_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->input('warnings') === 'active', fn ($q) => $q->where('active_warnings_count', '>', 0))
            ->when($request->input('warnings') === 'deducted', fn ($q) => $q->where('deducted_days', '>', 0))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->orderBy('name')
            ->get();

        return view('supervisors.index', compact('supervisors', 'classes', 'filters'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->orderBy('name')
            ->get();

        return view('supervisors.create', compact('classes'));
    }

    public function store(StoreSupervisorRequest $request): RedirectResponse
    {
        Supervisor::create($request->validated());

        return redirect()->route('supervisors.index')
            ->with('success', 'تم إضافة المشرف بنجاح.');
    }

    public function show(Supervisor $supervisor): View
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $supervisor->load([
            'schoolClass',
            'warnings.createdBy',
            'evaluations.evaluatedBy',
            'attendanceRecords.session',
        ]);

        $excusedRecords = $supervisor->attendanceRecords()
            ->where('status', 'excused')
            ->whereNotNull('excuse_attachment')
            ->with('session')
            ->latest()
            ->get();

        return view('supervisors.show', compact('supervisor', 'excusedRecords'));
    }

    public function print(Supervisor $supervisor): View
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $supervisor->load([
            'schoolClass',
            'warnings.createdBy',
            'evaluations.evaluatedBy',
            'attendanceRecords.session',
        ]);

        return view('supervisors.print', compact('supervisor'));
    }

    public function edit(Supervisor $supervisor): View
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $user = auth()->user();
        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->orderBy('name')
            ->get();

        return view('supervisors.edit', compact('supervisor', 'classes'));
    }

    public function update(UpdateSupervisorRequest $request, Supervisor $supervisor): RedirectResponse
    {
        $data = $request->validated();

        if (! auth()->user()->can('edit-supervisor-deductions')) {
            unset($data['deducted_days']);
        }

        $supervisor->update($data);

        return redirect()->route('supervisors.show', $supervisor)
            ->with('success', 'تم تحديث بيانات المشرف بنجاح.');
    }

    public function destroy(Supervisor $supervisor): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $supervisor->delete();

        return redirect()->route('supervisors.index')
            ->with('success', 'تم حذف المشرف بنجاح.');
    }

    public function importTemplate(): BinaryFileResponse
    {
        return Excel::download(new SupervisorsTemplateExport, 'template-mushref.xlsx');
    }

    public function import(ImportExcelRequest $request): RedirectResponse
    {
        $importService = new SupervisorImportService(auth()->user());
        $result = $importService->import($this->readExcelRows($request));

        return $this->redirectWithImportResult($result, 'supervisors.index', 'مشرف');
    }
}
