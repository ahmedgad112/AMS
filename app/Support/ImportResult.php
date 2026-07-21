<?php

namespace App\Support;

class ImportResult
{
    public int $imported = 0;

    public int $skipped = 0;

    /** @var array<int, string> */
    public array $errors = [];

    public function addError(int $row, string $message): void
    {
        $this->errors[$row] = $message;
    }

    public function skip(int $row, string $reason): void
    {
        $this->skipped++;
        $this->addError($row, $reason);
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function flashMessage(string $entityLabel): string
    {
        $message = "تم استيراد {$this->imported} {$entityLabel} بنجاح.";

        if ($this->skipped > 0) {
            $message .= " تم تخطي {$this->skipped} صف.";
        }

        return $message;
    }
}
