<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecordsRequest;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Support\ClassAuthorization;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();

        $classes = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user)
            ->withCount('supervisors')
            ->orderBy('name')
            ->get();

        $sessions = AttendanceSession::query()
            ->with(['schoolClass', 'createdBy'])
            ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
            ->when($request->filled('class_id'), fn ($q) => $q->where('school_class_id', $request->class_id))
            ->latest('date')
            ->paginate(15);

        return view('attendance.index', compact('classes', 'sessions'));
    }

    public function create(Request $request): RedirectResponse|View
    {
        $request->validate([
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'date' => ['nullable', 'date'],
        ], [
            'school_class_id.required' => 'يرجى اختيار الفصل.',
        ]);

        $schoolClass = SchoolClass::findOrFail($request->school_class_id);
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $schoolClass);

        $date = $request->input('date', now()->toDateString());

        try {
            $session = $this->attendanceService->openSession($schoolClass, auth()->user(), $date);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('attendance.sessions.show', $session);
    }

    public function show(AttendanceSession $session): View
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $session->school_class_id);

        $session->load(['schoolClass', 'records.supervisor']);

        $supervisors = $session->schoolClass->supervisors()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $recordsBySupervisor = $session->records->keyBy('supervisor_id');

        return view('attendance.session', compact('session', 'supervisors', 'recordsBySupervisor'));
    }

    public function storeRecords(StoreAttendanceRecordsRequest $request, AttendanceSession $session): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $session->school_class_id);

        if ($session->isClosed()) {
            return back()->with('error', 'الجلسة مغلقة ولا يمكن تعديلها.');
        }

        $recordsData = [];
        foreach ($request->input('records', []) as $index => $record) {
            $existing = $session->records()->where('supervisor_id', $record['supervisor_id'])->first();

            $recordsData[] = [
                'supervisor_id' => $record['supervisor_id'],
                'status' => $record['status'],
                'excuse_reason' => $record['excuse_reason'] ?? null,
                'excuse_attachment' => $request->file("records.{$index}.excuse_attachment"),
                'existing_attachment' => $existing?->excuse_attachment,
            ];
        }

        try {
            $this->attendanceService->saveRecords($session, $recordsData);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم حفظ سجل الحضور بنجاح.');
    }

    public function close(AttendanceSession $session): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $session->school_class_id);

        try {
            $this->attendanceService->closeSession($session);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إغلاق الجلسة بنجاح.');
    }

    public function reopen(AttendanceSession $session): RedirectResponse
    {
        if (! auth()->user()->can('reopen-sessions')) {
            abort(403);
        }

        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $session->school_class_id);

        $this->attendanceService->reopenSession($session);

        return back()->with('success', 'تم إعادة فتح الجلسة.');
    }

    public function destroy(AttendanceSession $session): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $session->school_class_id);

        try {
            $this->attendanceService->deleteSession($session);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('attendance.index')
            ->with('success', 'تم حذف جلسة الحضور بنجاح.');
    }

    public function destroyRecord(AttendanceSession $session, AttendanceRecord $record): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $session->school_class_id);

        try {
            $this->attendanceService->deleteRecord($session, $record);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم حذف سجل الحضور للمشرف.');
    }
}
