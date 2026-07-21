<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationRequest;
use App\Models\Evaluation;
use App\Models\Supervisor;
use App\Support\ClassAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function store(StoreEvaluationRequest $request, Supervisor $supervisor): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $supervisor->school_class_id);

        DB::transaction(function () use ($request, $supervisor) {
            Evaluation::create([
                'supervisor_id' => $supervisor->id,
                'score' => $request->input('score'),
                'notes' => $request->input('notes'),
                'evaluated_by_user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'تم حفظ التقييم بنجاح.');
    }
}
