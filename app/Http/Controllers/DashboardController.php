<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Models\Warning;
use App\Support\ClassAuthorization;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $classesQuery = ClassAuthorization::scopeAccessibleClasses(SchoolClass::query(), $user);

        $stats = [
            'classes_count' => (clone $classesQuery)->count(),
            'supervisors_count' => Supervisor::query()
                ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
                ->where('status', 'active')
                ->count(),
            'open_sessions' => AttendanceSession::query()
                ->where('status', 'open')
                ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
                ->count(),
            'warnings_this_month' => Warning::query()
                ->when(! $user->canAccessAllClasses(), function ($q) use ($user) {
                    $q->whereHas('supervisor', fn ($sq) => $sq->whereIn('school_class_id', $user->assignedClassIds()));
                })
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        $recentSessions = AttendanceSession::query()
            ->with(['schoolClass', 'createdBy'])
            ->when(! $user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $user->assignedClassIds()))
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentSessions'));
    }
}
