<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Rule: Admin creates Assignees only; Super Admin can also manage
     * Assignees from here (Admin creation lives in AdminController).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::where('role', 'assignee')
            ->when($request->filled('search'), fn ($q) => $q->where('full_name', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Settings/Users/Index', ['users' => $users, 'filters' => $request->only('search')]);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        abort_unless(request()->user()->can('createRole', [User::class, 'assignee']), 403);

        return Inertia::render('Settings/Users/Create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        abort_unless($request->user()->can('createRole', [User::class, 'assignee']), 403);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'assignee';

        $user = User::create($data);

        return redirect()->route('settings.users.index')->with('success', "Assignee {$user->full_name} created.");
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return Inertia::render('Settings/Users/Edit', ['targetUser' => $user]);
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('settings.users.index')->with('success', 'User updated.');
    }

    /**
     * Disable User - flips status to inactive rather than deleting.
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        AuditLog::write($user, $user->status === 'active' ? 'user_enabled' : 'user_disabled', null, ['status' => $user->status]);

        return back()->with('success', "User {$user->full_name} is now {$user->status}.");
    }

    /**
     * Reset Password - generates a temporary password (in production this
     * would email a reset link instead of returning the plaintext value).
     */
    public function resetPassword(User $user)
    {
        $this->authorize('resetPassword', $user);

        $tempPassword = Str::random(10);
        $user->update(['password' => Hash::make($tempPassword)]);

        AuditLog::write($user, 'password_reset', null, null);

        return back()->with('success', "Password reset. Temporary password: {$tempPassword}");
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect()->route('settings.users.index')->with('success', 'User removed.');
    }
}
