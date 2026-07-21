<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupervisorInquiryRequest;
use App\Models\Supervisor;
use Illuminate\View\View;

class PublicSupervisorEvaluationController extends Controller
{
    public function create(): View
    {
        return view('public.evaluation', [
            'supervisor' => null,
            'phone' => old('phone'),
        ]);
    }

    public function store(SupervisorInquiryRequest $request): View
    {
        $supervisor = Supervisor::findByPhone($request->input('phone'));

        if (! $supervisor) {
            return view('public.evaluation', [
                'supervisor' => null,
                'phone' => $request->input('phone'),
                'notFound' => true,
            ]);
        }

        $supervisor->load([
            'schoolClass',
            'evaluations' => fn ($q) => $q->latest(),
            'warnings' => fn ($q) => $q->latest(),
        ]);

        return view('public.evaluation', [
            'supervisor' => $supervisor,
            'phone' => $request->input('phone'),
            'latestEvaluation' => $supervisor->evaluations->first(),
        ]);
    }
}
