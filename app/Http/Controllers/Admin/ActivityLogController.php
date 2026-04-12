<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->input('user'));
        }

        if ($request->filled('search')) {
            $query->where('description', 'ilike', '%'.$request->input('search').'%');
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        return view('admin.activity-log.index', compact('logs', 'actions'));
    }
}
