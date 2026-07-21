<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Support\ImportResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolClassImportService
{
    public function import(Collection $rows): ImportResult
    {
        $result = new ImportResult;

        DB::transaction(function () use ($rows, $result) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $name = trim((string) ($row[0] ?? ''));

                if ($name === '') {
                    continue;
                }

                if (SchoolClass::where('name', $name)->exists()) {
                    $result->skip($rowNumber, "الفصل «{$name}» موجود مسبقاً.");

                    continue;
                }

                SchoolClass::create([
                    'name' => $name,
                    'code' => $this->generateUniqueCode($name),
                ]);

                $result->imported++;
            }
        });

        return $result;
    }

    protected function generateUniqueCode(string $name): string
    {
        $base = Str::upper(Str::slug($name, '-'));

        if ($base === '') {
            $base = 'CLASS';
        }

        $code = $base;
        $counter = 1;

        while (SchoolClass::where('code', $code)->exists()) {
            $code = $base.'-'.$counter;
            $counter++;
        }

        return $code;
    }
}
