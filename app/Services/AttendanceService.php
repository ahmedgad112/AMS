<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    public function openSession(SchoolClass $schoolClass, User $user, string $date): AttendanceSession
    {
        return DB::transaction(function () use ($schoolClass, $user, $date) {
            $session = AttendanceSession::firstOrCreate(
                [
                    'date' => $date,
                    'school_class_id' => $schoolClass->id,
                ],
                [
                    'created_by_user_id' => $user->id,
                    'status' => 'open',
                ]
            );

            if ($session->isClosed()) {
                throw new \RuntimeException('الجلسة مغلقة ولا يمكن تعديلها.');
            }

            return $session->load(['schoolClass', 'records.supervisor']);
        });
    }

    public function saveRecords(AttendanceSession $session, array $recordsData): AttendanceSession
    {
        if ($session->isClosed()) {
            throw new \RuntimeException('الجلسة مغلقة ولا يمكن تعديلها.');
        }

        return DB::transaction(function () use ($session, $recordsData) {
            foreach ($recordsData as $recordData) {
                $attachmentPath = null;

                if (! empty($recordData['excuse_attachment']) && $recordData['excuse_attachment'] instanceof UploadedFile) {
                    $attachmentPath = $recordData['excuse_attachment']->store('excuse-attachments', 'public');
                }

                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'supervisor_id' => $recordData['supervisor_id'],
                    ],
                    [
                        'status' => $recordData['status'],
                        'excuse_reason' => $recordData['status'] === 'excused' ? ($recordData['excuse_reason'] ?? null) : null,
                        'excuse_attachment' => $recordData['status'] === 'excused'
                            ? ($attachmentPath ?? $recordData['existing_attachment'] ?? null)
                            : null,
                    ]
                );
            }

            return $session->fresh(['records.supervisor', 'schoolClass']);
        });
    }

    public function grantExcuse(
        Supervisor $supervisor,
        User $user,
        string $date,
        string $reason,
        ?UploadedFile $attachment = null,
        bool $allowReopen = false
    ): AttendanceRecord {
        return DB::transaction(function () use ($supervisor, $user, $date, $reason, $attachment, $allowReopen) {
            $session = AttendanceSession::firstOrCreate(
                [
                    'date' => $date,
                    'school_class_id' => $supervisor->school_class_id,
                ],
                [
                    'created_by_user_id' => $user->id,
                    'status' => 'open',
                ]
            );

            if ($session->isClosed()) {
                if (! $allowReopen) {
                    throw new \RuntimeException('جلسة الحضور لهذا اليوم مغلقة. يرجى التواصل مع المسؤول لإعادة فتحها.');
                }

                $session->update(['status' => 'open']);
            }

            $existing = $session->records()->where('supervisor_id', $supervisor->id)->first();

            $attachmentPath = null;
            if ($attachment) {
                if ($existing?->excuse_attachment) {
                    $this->deleteAttachment($existing->excuse_attachment);
                }
                $attachmentPath = $attachment->store('excuse-attachments', 'public');
            }

            return AttendanceRecord::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'supervisor_id' => $supervisor->id,
                ],
                [
                    'status' => 'excused',
                    'excuse_reason' => $reason,
                    'excuse_attachment' => $attachmentPath ?? $existing?->excuse_attachment,
                ]
            );
        });
    }

    public function closeSession(AttendanceSession $session): AttendanceSession
    {
        if ($session->isClosed()) {
            throw new \RuntimeException('الجلسة مغلقة بالفعل.');
        }

        return DB::transaction(function () use ($session) {
            $session->update(['status' => 'closed']);

            return $session->fresh(['records.supervisor', 'schoolClass']);
        });
    }

    public function reopenSession(AttendanceSession $session): AttendanceSession
    {
        return DB::transaction(function () use ($session) {
            $session->update(['status' => 'open']);

            return $session->fresh(['records.supervisor', 'schoolClass']);
        });
    }

    public function deleteSession(AttendanceSession $session): void
    {
        DB::transaction(function () use ($session) {
            $session->load('records');

            foreach ($session->records as $record) {
                $this->deleteAttachment($record->excuse_attachment);
            }

            $session->delete();
        });
    }

    public function deleteRecord(AttendanceSession $session, AttendanceRecord $record): void
    {
        if ($session->isClosed()) {
            throw new \RuntimeException('الجلسة مغلقة ولا يمكن حذف السجلات.');
        }

        if ($record->attendance_session_id !== $session->id) {
            throw new \RuntimeException('السجل لا ينتمي لهذه الجلسة.');
        }

        DB::transaction(function () use ($record) {
            $this->deleteAttachment($record->excuse_attachment);
            $record->delete();
        });
    }

    public function deleteAttachment(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
