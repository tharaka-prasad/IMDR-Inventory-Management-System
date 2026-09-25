<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationEvents;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\SystemSetting;
use App\Models\User;
use App\Policies\AssignmentPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\LocationPolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\SystemSettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Inventory::class, InventoryPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
        Gate::policy(Maintenance::class, MaintenancePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(SystemSetting::class, SystemSettingPolicy::class);

        Event::listen(Login::class, [LogAuthenticationEvents::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationEvents::class, 'handleLogout']);
        Event::listen(Failed::class, [LogAuthenticationEvents::class, 'handleFailed']);
    }
}
