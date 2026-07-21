<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPermissions;
use App\Models\ActivityLog;
use App\Models\User;
use App\Support\ActivityLogPresenter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    use AuthorizesPermissions;

    public function index(Request $request): View
    {
        $this->authorizePermission('view-activity-log');

        $filters = $request->only(['search', 'log_name', 'event', 'causer_id', 'date_from', 'date_to']);

        $logs = ActivityLog::query()
            ->with(['causer', 'subject'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->input('search');
                $query->where('description', 'like', "%{$term}%");
            })
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->filled('event'), fn ($q) => $q->where('event', $request->event))
            ->when($request->filled('causer_id'), fn ($q) => $q->where('causer_id', $request->causer_id))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('activity-log.index', [
            'logs' => $logs,
            'filters' => $filters,
            'logNames' => ActivityLogPresenter::logNames(),
            'events' => ActivityLogPresenter::events(),
            'users' => $users,
        ]);
    }
}
