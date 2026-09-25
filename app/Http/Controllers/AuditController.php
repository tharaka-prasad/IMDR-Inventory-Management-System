<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $logs = AuditLog::with('user:id,full_name,email')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->get('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->get('to')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Settings/Audit/Index', [
            'logs' => $logs,
            'filters' => $request->only(['user_id', 'from', 'to', 'action']),
            'users' => User::orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }
}
