<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Rule: "Cannot create Admin" applies to the Admin role, so every
     * action here is additionally gated by the role:super_admin route
     * middleware as well as these in-controller checks.
     */
    public function index(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $admins = User::where('role', 'admin')
            ->when($request->filled('search'), fn ($q) => $q->where('full_name', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Settings/Admins/Index', ['admins' => $admins, 'filters' => $request->only('search')]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        return Inertia::render('Settings/Admins/Create');
    }

    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'admin';

        $admin = User::create($data);

        return redirect()->route('settings.admins.index')->with('success', "Admin {$admin->full_name} created.");
    }

    public function edit(Request $request, User $admin)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        return Inertia::render('Settings/Admins/Edit', ['admin' => $admin]);
    }

    public function update(StoreAdminRequest $request, User $admin)
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return redirect()->route('settings.admins.index')->with('success', 'Admin updated.');
    }

    /**
     * Change Role - Super Admin can promote/demote between admin and assignee.
     */
    public function changeRole(Request $request, User $admin)
    {
        abort_unless($request->user()->can('changeRole', $admin), 403);

        $request->validate(['role' => 'required|in:admin,assignee']);
        $old = $admin->role;
        $admin->update(['role' => $request->string('role')]);

        AuditLog::write($admin, 'role_changed', ['role' => $old], ['role' => $admin->role]);

        return back()->with('success', "Role changed from {$old} to {$admin->role}.");
    }

    /**
     * Disable Admin.
     */
    public function toggleStatus(Request $request, User $admin)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $admin->status = $admin->status === 'active' ? 'inactive' : 'active';
        $admin->save();

        AuditLog::write($admin, $admin->status === 'active' ? 'admin_enabled' : 'admin_disabled', null, ['status' => $admin->status]);

        return back()->with('success', "Admin {$admin->full_name} is now {$admin->status}.");
    }
}
