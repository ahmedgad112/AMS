<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarningRequest;
use App\Models\Supervisor;
use App\Services\WarningService;
use App\Support\ClassAuthorization;
use Illuminate\Http\RedirectResponse;

class WarningController extends Controller
{
    public function __construct(
        protected WarningService $warningService
    ) {}

    public function store(StoreWarningRequest $request, Supervisor $supervisor): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        $warning = $this->warningService->issueWarning(
            $supervisor,
            $request->input('reason'),
            auth()->user()
        );

        $message = $warning->triggered_deduction
            ? 'تم تسجيل الإنذار الثالث وخصم 14 يوماً تلقائياً من أيام التدريب.'
            : "تم تسجيل الإنذار رقم {$warning->warning_level} بنجاح.";

        return back()->with('success', $message);
    }
}
