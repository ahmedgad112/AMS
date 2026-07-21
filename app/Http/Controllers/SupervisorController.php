<?php

namespace App\Http\Controllers;

use App\Exports\SupervisorsTemplateExport;
use App\Http\Controllers\Concerns\AuthorizesPermissions;
use App\Http\Controllers\Concerns\HandlesExcelImport;
use App\Services\ActivityLogger;
use App\Http\Requests\BulkDeleteSupervisorsRequest;
use App\Http\Requests\BulkUpdateTrainingDaysRequest;
use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\StoreSupervisorRequest;
use App\Http\Requests\UpdateSupervisorRequest;
use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Models\User;
use App\Services\SupervisorImportService;
use App\Support\ClassAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupervisorController extends Controller
{
    use AuthorizesPermissions, HandlesExcelImport;

    public function index(Request $request): View
    {
        $this->authorizePermission('view-supervisors');

        $user = auth()->user();
        $filters = $request->only(['search', 'school_class_id', 'status', 'warnings']);

        $supervisors = $this->filteredSupervisorsQuery($request, $user)
            ->with('schoolClass')
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
        $this->authorizePermission('create-supervisors');

        $user = auth()->user();
        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->orderBy('name')
            ->get();

        return view('supervisors.create', compact('classes'));
    }

    public function store(StoreSupervisorRequest $request): RedirectResponse
    {
        $this->authorizePermission('create-supervisors');

        Supervisor::create($request->validated());

        return redirect()->route('supervisors.index')
            ->with('success', 'تم إضافة المشرف بنجاح.');
    }

    public function show(Supervisor $supervisor): View
    {
        $this->authorizePermission('view-supervisors');

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
        $this->authorizePermission('print-supervisors');

        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $supervisor->load([
            'schoolClass',
            'warnings.createdBy',
            'evaluations.evaluatedBy',
            'attendanceRecords.session',
        ]);

        ActivityLogger::log(
            "طباعة كارت المشرف «{$supervisor->name}»",
            'print',
            'supervisors',
            $supervisor
        );

        return view('supervisors.print', compact('supervisor'));
    }

    public function edit(Supervisor $supervisor): View
    {
        $this->authorizePermission('edit-supervisors');

        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $user = auth()->user();
        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->orderBy('name')
            ->get();

        return view('supervisors.edit', compact('supervisor', 'classes'));
    }

    public function update(UpdateSupervisorRequest $request, Supervisor $supervisor): RedirectResponse
    {
        $this->authorizePermission('edit-supervisors');

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
        $this->authorizePermission('delete-supervisors');

        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $supervisor->delete();

        return redirect()->route('supervisors.index')
            ->with('success', 'تم حذف المشرف بنجاح.');
    }

    public function bulkUpdateTrainingDays(BulkUpdateTrainingDaysRequest $request): RedirectResponse
    {
        $this->authorizePermission('edit-supervisors');

        $user = auth()->user();

        $query = Supervisor::query()
            ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
            ->when($request->filled('school_class_id'), function ($q) use ($request, $user) {
                ClassAuthorization::abortUnlessCanAccess($user, $request->integer('school_class_id'));
                $q->where('school_class_id', $request->school_class_id);
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        $count = (clone $query)->count();

        if ($count === 0) {
            return back()
                ->withInput()
                ->with('error', 'لا يوجد مشرفين مطابقين للمعايير المحددة.');
        }

        $days = $request->integer('total_training_days');
        $query->update(['total_training_days' => $days]);

        ActivityLogger::log(
            "تحديث أيام التدريب إلى {$days} لـ {$count} مشرف",
            'bulk_update',
            'supervisors',
            null,
            [
                'total_training_days' => $days,
                'count' => $count,
                'school_class_id' => $request->input('school_class_id'),
                'status' => $request->input('status'),
            ]
        );

        return redirect()->route('supervisors.index')
            ->with('success', "تم تحديث أيام التدريب لـ {$count} مشرف بنجاح.");
    }

    public function bulkDestroy(BulkDeleteSupervisorsRequest $request): RedirectResponse
    {
        $this->authorizePermission('delete-supervisors');

        $user = auth()->user();

        if ($request->boolean('delete_all_filtered')) {
            $supervisors = $this->filteredSupervisorsQuery($request, $user)->get();
        } else {
            $ids = $request->input('supervisor_ids', []);

            $supervisors = Supervisor::query()
                ->whereIn('id', $ids)
                ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
                ->get();

            if ($supervisors->count() !== count($ids)) {
                abort(403);
            }
        }

        if ($supervisors->isEmpty()) {
            return back()->with('error', 'لم يتم تحديد أي مشرفين للحذف.');
        }

        foreach ($supervisors as $supervisor) {
            ClassAuthorization::abortUnlessCanAccess($user, $supervisor->school_class_id);
        }

        $count = $supervisors->count();

        foreach ($supervisors as $supervisor) {
            $supervisor->delete();
        }

        ActivityLogger::log(
            "حذف {$count} مشرف دفعة واحدة",
            'bulk_delete',
            'supervisors',
            null,
            [
                'count' => $count,
                'delete_all_filtered' => $request->boolean('delete_all_filtered'),
                'filters' => $request->only(['search', 'school_class_id', 'status', 'warnings']),
            ]
        );

        return back()->with('success', "تم حذف {$count} مشرف بنجاح.");
    }

    public function importTemplate(): BinaryFileResponse
    {
        $this->authorizePermission('import-supervisors');

        return Excel::download(new SupervisorsTemplateExport, 'template-mushref.xlsx');
    }

    public function import(ImportExcelRequest $request): RedirectResponse
    {
        $this->authorizePermission('import-supervisors');
        $importService = new SupervisorImportService(auth()->user());
        $result = $importService->import($this->readExcelRows($request));

        return $this->redirectWithImportResult($result, 'supervisors.index', 'مشرف');
    }

    private function filteredSupervisorsQuery(Request $request, User $user)
    {
        return Supervisor::query()
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
            ->when($request->input('warnings') === 'deducted', fn ($q) => $q->where('deducted_days', '>', 0));
    }
}
