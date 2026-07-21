<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPermissions;
use App\Exports\AttendanceRecordsExport;
use App\Exports\EvaluationsExport;
use App\Exports\SupervisorDetailExport;
use App\Exports\SupervisorsSummaryExport;
use App\Exports\WarningsExport;
use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Support\ClassAuthorization;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    use AuthorizesPermissions;

    public function index(Request $request): View
    {
        $this->authorizePermission('view-reports');

        $user = auth()->user();
        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->orderBy('name')
            ->get();

        return view('reports.index', [
            'classes' => $classes,
            'filters' => $request->only(['class_id', 'date_from', 'date_to']),
        ]);
    }

    public function supervisors(Request $request): BinaryFileResponse
    {
        $this->validateFilters($request);

        $user = auth()->user();
        $classId = $request->integer('class_id') ?: null;

        if ($classId) {
            ClassAuthorization::abortUnlessCanAccess($user, $classId);
        }

        $filename = 'supervisors-summary-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new SupervisorsSummaryExport($user, $classId),
            $filename
        );
    }

    public function attendance(Request $request): BinaryFileResponse
    {
        $this->validateFilters($request);

        $user = auth()->user();
        $classId = $request->integer('class_id') ?: null;

        if ($classId) {
            ClassAuthorization::abortUnlessCanAccess($user, $classId);
        }

        $filename = 'attendance-records-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new AttendanceRecordsExport(
                $user,
                $classId,
                $request->input('date_from'),
                $request->input('date_to'),
            ),
            $filename
        );
    }

    public function warnings(Request $request): BinaryFileResponse
    {
        $this->validateFilters($request);

        $user = auth()->user();
        $classId = $request->integer('class_id') ?: null;

        if ($classId) {
            ClassAuthorization::abortUnlessCanAccess($user, $classId);
        }

        $filename = 'warnings-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new WarningsExport(
                $user,
                $classId,
                $request->input('date_from'),
                $request->input('date_to'),
            ),
            $filename
        );
    }

    public function evaluations(Request $request): BinaryFileResponse
    {
        $this->validateFilters($request);

        if (! auth()->user()->can('export-reports')) {
            abort(403);
        }

        $user = auth()->user();
        $classId = $request->integer('class_id') ?: null;

        if ($classId) {
            ClassAuthorization::abortUnlessCanAccess($user, $classId);
        }

        $filename = 'evaluations-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new EvaluationsExport(
                $user,
                $classId,
                $request->input('date_from'),
                $request->input('date_to'),
            ),
            $filename
        );
    }

    public function supervisor(Supervisor $supervisor): BinaryFileResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $filename = 'supervisor-'.str($supervisor->name)->slug().'-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new SupervisorDetailExport($supervisor),
            $filename
        );
    }

    protected function validateFilters(Request $request): void
    {
        $request->validate([
            'class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ], [
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
        ]);
    }
}
