<?php

namespace App\Http\Controllers\Concerns;

use App\Http\Requests\ImportExcelRequest;
use App\Imports\RawSheetImport;
use App\Services\ActivityLogger;
use App\Support\ImportResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

trait HandlesExcelImport
{
    protected function readExcelRows(ImportExcelRequest $request): Collection
    {
        $sheet = Excel::toArray(new RawSheetImport, $request->file('file'));

        return collect($sheet[0] ?? [])->slice(1)->values();
    }

    protected function redirectWithImportResult(ImportResult $result, string $redirectRoute, string $entityLabel): RedirectResponse
    {
        $redirect = redirect()->route($redirectRoute);

        if ($result->imported > 0) {
            $redirect->with('success', $result->flashMessage($entityLabel));
        } elseif ($result->skipped > 0) {
            $redirect->with('error', 'لم يتم استيراد أي سجل. تم تخطي جميع الصفوف.');
        } else {
            $redirect->with('error', 'الملف فارغ أو لا يحتوي على بيانات.');
        }

        if ($result->hasErrors()) {
            $redirect->with('import_errors', $result->errors);
        }

        ActivityLogger::log(
            "استيراد {$entityLabel} من Excel",
            'import',
            'imports',
            null,
            [
                'entity' => $entityLabel,
                'imported' => $result->imported,
                'skipped' => $result->skipped,
                'route' => $redirectRoute,
            ]
        );

        return $redirect;
    }
}
