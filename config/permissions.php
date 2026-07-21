<?php

return [

    'groups' => [
        'system' => [
            'label' => 'النظام',
            'permissions' => [
                'view-users' => 'عرض المستخدمين',
                'create-users' => 'إضافة مستخدم',
                'edit-users' => 'تعديل مستخدم',
                'delete-users' => 'حذف مستخدم',
                'impersonate-users' => 'الدخول بحساب مستخدم آخر',
                'view-roles' => 'عرض الأدوار والصلاحيات',
                'create-roles' => 'إنشاء دور جديد',
                'edit-roles' => 'تعديل صلاحيات الدور',
                'delete-roles' => 'حذف دور',
            ],
        ],
        'classes' => [
            'label' => 'الفصول والورش',
            'permissions' => [
                'view-classes' => 'عرض الفصول',
                'create-classes' => 'إضافة فصل',
                'edit-classes' => 'تعديل فصل',
                'delete-classes' => 'حذف فصل',
                'import-classes' => 'استيراد الفصول من Excel',
                'access-all-classes' => 'الوصول لجميع الفصول (بدون إسناد)',
            ],
        ],
        'supervisors' => [
            'label' => 'المشرفين',
            'permissions' => [
                'view-supervisors' => 'عرض المشرفين وكروتهم',
                'create-supervisors' => 'إضافة مشرف',
                'edit-supervisors' => 'تعديل مشرف',
                'delete-supervisors' => 'حذف مشرف',
                'import-supervisors' => 'استيراد المشرفين من Excel',
                'print-supervisors' => 'طباعة كارت المشرف',
                'edit-supervisor-deductions' => 'تعديل أيام الخصم يدوياً',
            ],
        ],
        'attendance' => [
            'label' => 'الحضور والغياب',
            'permissions' => [
                'view-attendance' => 'عرض جلسات الحضور',
                'create-attendance-sessions' => 'فتح جلسة حضور جديدة',
                'save-attendance-records' => 'حفظ سجل الحضور',
                'close-attendance-sessions' => 'إغلاق الجلسة',
                'delete-attendance-sessions' => 'حذف جلسة حضور',
                'delete-attendance-records' => 'مسح سجل حضور مشرف',
                'reopen-sessions' => 'إعادة فتح الجلسات المغلقة',
            ],
        ],
        'warnings' => [
            'label' => 'الإنذارات',
            'permissions' => [
                'view-warnings' => 'عرض سجل الإنذارات',
                'create-warnings' => 'تسجيل إنذار / مخالفة',
            ],
        ],
        'evaluations' => [
            'label' => 'التقييمات',
            'permissions' => [
                'view-evaluations' => 'عرض التقييمات',
                'create-evaluations' => 'إدخال تقييم جديد',
            ],
        ],
        'reports' => [
            'label' => 'التقارير',
            'permissions' => [
                'view-reports' => 'عرض صفحة التقارير',
                'export-reports' => 'تصدير تقارير Excel',
            ],
        ],
    ],

    /*
    | ترحيل أسماء الصلاحيات القديمة (manage-*) إلى الأسماء الجديدة (view-*).
    */
    'legacy_map' => [
        'manage-users' => 'view-users',
        'manage-roles' => 'view-roles',
        'manage-classes' => 'view-classes',
        'manage-supervisors' => 'view-supervisors',
        'manage-attendance' => 'view-attendance',
        'manage-warnings' => 'create-warnings',
        'manage-evaluations' => ['view-evaluations', 'create-evaluations'],
    ],

    'protected_roles' => [
        'super-admin',
    ],

];
