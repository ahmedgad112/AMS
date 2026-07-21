<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupervisorExcuseRequest;
use App\Models\Supervisor;
use App\Services\AttendanceService;
use App\Support\ClassAuthorization;
use Illuminate\Http\RedirectResponse;

class SupervisorExcuseController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function store(StoreSupervisorExcuseRequest $request, Supervisor $supervisor): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        try {
            $this->attendanceService->grantExcuse(
                $supervisor,
                auth()->user(),
                $request->input('date'),
                $request->input('excuse_reason'),
                $request->file('excuse_attachment'),
                auth()->user()->can('reopen-sessions')
            );
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم تسجيل عذر الغياب بنجاح.');
    }
}
