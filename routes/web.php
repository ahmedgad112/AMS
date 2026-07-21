<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\PublicSupervisorEvaluationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\SupervisorExcuseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarningController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:20,1')->group(function () {
    Route::get('evaluation', [PublicSupervisorEvaluationController::class, 'create'])
        ->name('public.evaluation');
    Route::post('evaluation', [PublicSupervisorEvaluationController::class, 'store'])
        ->name('public.evaluation.lookup');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::post('impersonate/leave', [ImpersonationController::class, 'destroy'])
        ->name('impersonate.leave');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('permission:view-activity-log')->get('activity-log', [ActivityLogController::class, 'index'])
        ->name('activity-log.index');

    Route::middleware('permission:view-classes')->get('school-classes', [SchoolClassController::class, 'index'])
        ->name('school-classes.index');
    Route::middleware('permission:create-classes')->group(function () {
        Route::get('school-classes/create', [SchoolClassController::class, 'create'])->name('school-classes.create');
        Route::post('school-classes', [SchoolClassController::class, 'store'])->name('school-classes.store');
    });
    Route::middleware('permission:edit-classes')->group(function () {
        Route::get('school-classes/{school_class}/edit', [SchoolClassController::class, 'edit'])->name('school-classes.edit');
        Route::put('school-classes/{school_class}', [SchoolClassController::class, 'update'])->name('school-classes.update');
    });
    Route::middleware('permission:delete-classes')->delete('school-classes/{school_class}', [SchoolClassController::class, 'destroy'])
        ->name('school-classes.destroy');
    Route::middleware('permission:import-classes')->group(function () {
        Route::get('school-classes/import/template', [SchoolClassController::class, 'importTemplate'])
            ->name('school-classes.import.template');
        Route::post('school-classes/import', [SchoolClassController::class, 'import'])
            ->name('school-classes.import');
    });

    Route::middleware('permission:view-users')->get('users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:create-users')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:edit-users')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:delete-users')->delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');
    Route::middleware('permission:impersonate-users')->post('users/{user}/impersonate', [ImpersonationController::class, 'store'])
        ->name('users.impersonate');

    Route::middleware('permission:view-roles')->get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::middleware('permission:create-roles')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:edit-roles')->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware('permission:delete-roles')->delete('roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy');

    Route::middleware('permission:view-supervisors')->get('supervisors', [SupervisorController::class, 'index'])
        ->name('supervisors.index');
    Route::middleware('permission:create-supervisors')->group(function () {
        Route::get('supervisors/create', [SupervisorController::class, 'create'])->name('supervisors.create');
        Route::post('supervisors', [SupervisorController::class, 'store'])->name('supervisors.store');
    });
    Route::middleware('permission:import-supervisors')->group(function () {
        Route::get('supervisors/import/template', [SupervisorController::class, 'importTemplate'])
            ->name('supervisors.import.template');
        Route::post('supervisors/import', [SupervisorController::class, 'import'])
            ->name('supervisors.import');
    });
    Route::middleware('permission:view-supervisors')->get('supervisors/{supervisor}', [SupervisorController::class, 'show'])
        ->name('supervisors.show');
    Route::middleware('permission:edit-supervisors')->group(function () {
        Route::get('supervisors/{supervisor}/edit', [SupervisorController::class, 'edit'])->name('supervisors.edit');
        Route::put('supervisors/{supervisor}', [SupervisorController::class, 'update'])->name('supervisors.update');
    });
    Route::middleware('permission:delete-supervisors')->delete('supervisors/{supervisor}', [SupervisorController::class, 'destroy'])
        ->name('supervisors.destroy');
    Route::middleware('permission:print-supervisors')->get('supervisors/{supervisor}/print', [SupervisorController::class, 'print'])
        ->name('supervisors.print');

    Route::middleware('permission:view-attendance')->prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceSessionController::class, 'index'])->name('index');
        Route::get('/sessions/{session}', [AttendanceSessionController::class, 'show'])->name('sessions.show');
    });
    Route::middleware('permission:create-attendance-sessions')->post('/attendance/sessions', [AttendanceSessionController::class, 'create'])
        ->name('attendance.sessions.create');
    Route::middleware('permission:save-attendance-records')->post('/attendance/sessions/{session}/records', [AttendanceSessionController::class, 'storeRecords'])
        ->name('attendance.sessions.records.store');
    Route::middleware('permission:close-attendance-sessions')->post('/attendance/sessions/{session}/close', [AttendanceSessionController::class, 'close'])
        ->name('attendance.sessions.close');
    Route::middleware('permission:reopen-sessions')->post('/attendance/sessions/{session}/reopen', [AttendanceSessionController::class, 'reopen'])
        ->name('attendance.sessions.reopen');
    Route::middleware('permission:delete-attendance-sessions')->delete('/attendance/sessions/{session}', [AttendanceSessionController::class, 'destroy'])
        ->name('attendance.sessions.destroy');
    Route::middleware('permission:delete-attendance-records')->delete('/attendance/sessions/{session}/records/{record}', [AttendanceSessionController::class, 'destroyRecord'])
        ->name('attendance.sessions.records.destroy');

    Route::middleware('permission:create-warnings')->post('supervisors/{supervisor}/warnings', [WarningController::class, 'store'])
        ->name('supervisors.warnings.store');
    Route::middleware('permission:save-attendance-records')->post('supervisors/{supervisor}/excuses', [SupervisorExcuseController::class, 'store'])
        ->name('supervisors.excuses.store');

    Route::middleware('permission:view-reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
    });
    Route::middleware('permission:export-reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/supervisors', [ReportController::class, 'supervisors'])->name('supervisors');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/warnings', [ReportController::class, 'warnings'])->name('warnings');
        Route::get('/evaluations', [ReportController::class, 'evaluations'])->name('evaluations');
        Route::get('/supervisor/{supervisor}', [ReportController::class, 'supervisor'])->name('supervisor');
    });

    Route::middleware('permission:create-evaluations')->post('supervisors/{supervisor}/evaluations', [EvaluationController::class, 'store'])
        ->name('supervisors.evaluations.store');
});
