<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * Registered in AppServiceProvider (see EventServiceProvider note below) so
 * every login, logout, and failed login attempt is written to the audit
 * log, satisfying "Complete Audit Log for ... login action".
 */
class LogAuthenticationEvents
{
    public function handleLogin(Login $event): void
    {
        AuditLog::write($event->user, 'login', null, ['email' => $event->user->email], $event->user->id);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            AuditLog::write($event->user, 'logout', null, null, $event->user->id);
        }
    }

    public function handleFailed(Failed $event): void
    {
        AuditLog::write('users', 'login_failed', null, ['email' => $event->credentials['email'] ?? null], $event->user?->id);
    }
}
