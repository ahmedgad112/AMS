<?php

namespace App\Http\Controllers;

use App\Exports\SchoolClassesTemplateExport;
use App\Http\Controllers\Concerns\HandlesExcelImport;
use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\SchoolClassImportService;
use App\Support\ClassAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SchoolClassController extends Controller
{
    use HandlesExcelImport;
    public function index(): View
    {
        $user = auth()->user();

        $classes = ClassAuthorization::scopeAccessibleClasses(
            SchoolClass::query()->withCount(['supervisors', 'inspectors']),
            $user
        )->latest()->paginate(15);

        return view('school-classes.index', compact('classes'));
    }

    public function create(): View
    {
        $inspectors = User::permission('manage-attendance')->orderBy('name')->get();

        return view('school-classes.create', compact('inspectors'));
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $class = SchoolClass::create($request->safe()->only(['name', 'code', 'location']));

            if ($request->filled('inspector_ids')) {
                $class->inspectors()->sync($request->input('inspector_ids'));
            }
        });

        return redirect()->route('school-classes.index')
            ->with('success', 'تم إضافة الفصل بنجاح.');
    }

    public function edit(SchoolClass $schoolClass): View
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $schoolClass);

        $schoolClass->load('inspectors');
        $inspectors = User::permission('manage-attendance')->orderBy('name')->get();

        return view('school-classes.edit', compact('schoolClass', 'inspectors'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $schoolClass);

        DB::transaction(function () use ($request, $schoolClass) {
            $schoolClass->update($request->safe()->only(['name', 'code', 'location']));
            $schoolClass->inspectors()->sync($request->input('inspector_ids', []));
        });

        return redirect()->route('school-classes.index')
            ->with('success', 'تم تحديث الفصل بنجاح.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        ClassAuthorization::abortUnlessCanAccess(auth()->user(), $schoolClass);

        if (! auth()->user()->can('delete-classes')) {
            abort(403);
        }

        if ($schoolClass->supervisors()->exists()) {
            return redirect()->route('school-classes.index')
                ->with('error', 'لا يمكن حذف فصل مرتبط بمشرفين. يرجى حذف أو نقل المشرفين أولاً.');
        }

        $schoolClass->delete();

        return redirect()->route('school-classes.index')
            ->with('success', 'تم حذف الفصل بنجاح.');
    }

    public function importTemplate(): BinaryFileResponse
    {
        return Excel::download(new SchoolClassesTemplateExport, 'template-fasl.xlsx');
    }

    public function import(ImportExcelRequest $request, SchoolClassImportService $importService): RedirectResponse
    {
        $result = $importService->import($this->readExcelRows($request));

        return $this->redirectWithImportResult($result, 'school-classes.index', 'فصل');
    }
}
