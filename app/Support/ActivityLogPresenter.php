<?php

namespace App\Support;

class ActivityLogPresenter
{
    public static function logNameLabel(string $logName): string
    {
        return match ($logName) {
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'classes' => 'الفصول',
            'supervisors' => 'المشرفين',
            'attendance' => 'الحضور',
            'warnings' => 'الإنذارات',
            'evaluations' => 'التقييمات',
            'reports' => 'التقارير',
            'auth' => 'الدخول والخروج',
            'imports' => 'الاستيراد',
            'system' => 'النظام',
            default => $logName,
        };
    }

    public static function eventLabel(string $event): string
    {
        return match ($event) {
            'created' => 'إضافة',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'impersonate_start' => 'دخول كـ مستخدم',
            'impersonate_stop' => 'العودة للحساب',
            'export' => 'تصدير',
            'import' => 'استيراد',
            'print' => 'طباعة',
            'opened' => 'فتح',
            'closed' => 'إغلاق',
            'reopened' => 'إعادة فتح',
            default => $event,
        };
    }

    public static function eventBadgeClass(string $event): string
    {
        return match ($event) {
            'created', 'login', 'opened', 'reopened' => 'bg-emerald-100 text-emerald-800',
            'updated', 'import' => 'bg-blue-100 text-blue-800',
            'deleted', 'logout' => 'bg-red-100 text-red-800',
            'export', 'print' => 'bg-purple-100 text-purple-800',
            'impersonate_start', 'impersonate_stop' => 'bg-amber-100 text-amber-800',
            'closed' => 'bg-slate-200 text-slate-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    /** @return array<string, string> */
    public static function logNames(): array
    {
        return [
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'classes' => 'الفصول',
            'supervisors' => 'المشرفين',
            'attendance' => 'الحضور',
            'warnings' => 'الإنذارات',
            'evaluations' => 'التقييمات',
            'reports' => 'التقارير',
            'auth' => 'الدخول والخروج',
            'imports' => 'الاستيراد',
            'system' => 'النظام',
        ];
    }

    /** @return array<string, string> */
    public static function events(): array
    {
        return [
            'created' => 'إضافة',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'impersonate_start' => 'دخول كـ مستخدم',
            'impersonate_stop' => 'العودة للحساب',
            'export' => 'تصدير',
            'import' => 'استيراد',
            'print' => 'طباعة',
            'opened' => 'فتح',
            'closed' => 'إغلاق',
            'reopened' => 'إعادة فتح',
        ];
    }
}
