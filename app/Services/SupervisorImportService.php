<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Models\User;
use App\Support\ClassAuthorization;
use App\Support\ImportResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupervisorImportService
{
    public function __construct(
        protected User $user,
    ) {}

    public function import(Collection $rows): ImportResult
    {
        $result = new ImportResult;

        DB::transaction(function () use ($rows, $result) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                $name = trim((string) ($row[0] ?? ''));
                $phone = trim((string) ($row[1] ?? ''));
                $className = trim((string) ($row[2] ?? ''));

                if ($name === '' && $className === '') {
                    continue;
                }

                if ($name === '') {
                    $result->skip($rowNumber, 'اسم المشرف مطلوب.');

                    continue;
                }

                if ($className === '') {
                    $result->skip($rowNumber, 'اسم الفصل مطلوب.');

                    continue;
                }

                $schoolClass = SchoolClass::where('name', $className)->first();

                if (! $schoolClass) {
                    $result->skip($rowNumber, "الفصل «{$className}» غير موجود.");

                    continue;
                }

                if (! ClassAuthorization::canAccessClass($this->user, $schoolClass)) {
                    $result->skip($rowNumber, "ليس لديك صلاحية على الفصل «{$className}».");

                    continue;
                }

                $exists = Supervisor::where('name', $name)
                    ->where('school_class_id', $schoolClass->id)
                    ->exists();

                if ($exists) {
                    $result->skip($rowNumber, "المشرف «{$name}» مسجل مسبقاً في هذا الفصل.");

                    continue;
                }

                Supervisor::create([
                    'name' => $name,
                    'phone' => $phone !== '' ? $phone : null,
                    'school_class_id' => $schoolClass->id,
                    'status' => 'active',
                ]);

                $result->imported++;
            }
        });

        return $result;
    }
}
