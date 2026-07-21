<?php

use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PublicSupervisorEvaluationController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SupervisorController;
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

    Route::middleware('permission:manage-classes')->group(function () {
        Route::get('school-classes/import/template', [SchoolClassController::class, 'importTemplate'])
            ->name('school-classes.import.template');
        Route::post('school-classes/import', [SchoolClassController::class, 'import'])
            ->name('school-classes.import');
        Route::resource('school-classes', SchoolClassController::class)->except(['show']);
    });

    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/impersonate', [ImpersonationController::class, 'store'])
            ->name('users.impersonate');
    });

    Route::middleware('permission:manage-roles')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
    });

    Route::middleware('permission:manage-supervisors')->group(function () {
        Route::get('supervisors/import/template', [SupervisorController::class, 'importTemplate'])
            ->name('supervisors.import.template');
        Route::post('supervisors/import', [SupervisorController::class, 'import'])
            ->name('supervisors.import');
        Route::resource('supervisors', SupervisorController::class);
        Route::get('supervisors/{supervisor}/print', [SupervisorController::class, 'print'])
            ->name('supervisors.print');
    });

    Route::middleware('permission:manage-attendance')->prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceSessionController::class, 'index'])->name('index');
        Route::post('/sessions', [AttendanceSessionController::class, 'create'])->name('sessions.create');
        Route::get('/sessions/{session}', [AttendanceSessionController::class, 'show'])->name('sessions.show');
        Route::post('/sessions/{session}/records', [AttendanceSessionController::class, 'storeRecords'])
            ->name('sessions.records.store');
        Route::post('/sessions/{session}/close', [AttendanceSessionController::class, 'close'])
            ->name('sessions.close');
        Route::post('/sessions/{session}/reopen', [AttendanceSessionController::class, 'reopen'])
            ->name('sessions.reopen');
        Route::delete('/sessions/{session}', [AttendanceSessionController::class, 'destroy'])
            ->name('sessions.destroy');
        Route::delete('/sessions/{session}/records/{record}', [AttendanceSessionController::class, 'destroyRecord'])
            ->name('sessions.records.destroy');
    });

    Route::middleware('permission:manage-warnings')->group(function () {
        Route::post('supervisors/{supervisor}/warnings', [WarningController::class, 'store'])
            ->name('supervisors.warnings.store');
    });

    Route::middleware('permission:export-reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/supervisors', [ReportController::class, 'supervisors'])->name('supervisors');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/warnings', [ReportController::class, 'warnings'])->name('warnings');
        Route::get('/evaluations', [ReportController::class, 'evaluations'])->name('evaluations');
        Route::get('/supervisor/{supervisor}', [ReportController::class, 'supervisor'])->name('supervisor');
    });

    Route::middleware('permission:manage-evaluations')->group(function () {
        Route::post('supervisors/{supervisor}/evaluations', [EvaluationController::class, 'store'])
            ->name('supervisors.evaluations.store');
    });
});
