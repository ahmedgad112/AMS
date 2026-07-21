<?php

return [

    'groups' => [
        'system' => [
            'label' => 'النظام',
            'permissions' => [
                'manage-users' => 'إدارة المستخدمين',
                'manage-roles' => 'إدارة الأدوار والصلاحيات',
            ],
        ],
        'classes' => [
            'label' => 'الفصول والورش',
            'permissions' => [
                'manage-classes' => 'عرض وإضافة وتعديل الفصول',
                'delete-classes' => 'حذف الفصول',
                'access-all-classes' => 'الوصول لجميع الفصول (بدون إسناد)',
            ],
        ],
        'supervisors' => [
            'label' => 'المشرفين',
            'permissions' => [
                'manage-supervisors' => 'عرض وإضافة وتعديل وحذف المشرفين',
                'edit-supervisor-deductions' => 'تعديل أيام الخصم يدوياً',
            ],
        ],
        'attendance' => [
            'label' => 'الحضور والغياب',
            'permissions' => [
                'manage-attendance' => 'إدارة وحذف الحضور والغياب',
                'reopen-sessions' => 'إعادة فتح الجلسات المغلقة',
            ],
        ],
        'warnings' => [
            'label' => 'الإنذارات',
            'permissions' => [
                'manage-warnings' => 'تسجيل الإنذارات والمخالفات',
            ],
        ],
        'evaluations' => [
            'label' => 'التقييمات',
            'permissions' => [
                'manage-evaluations' => 'إدخال وإدارة التقييمات',
            ],
        ],
        'reports' => [
            'label' => 'التقارير',
            'permissions' => [
                'export-reports' => 'تصدير تقارير Excel',
            ],
        ],
    ],

    'protected_roles' => [
        'super-admin',
    ],

];
