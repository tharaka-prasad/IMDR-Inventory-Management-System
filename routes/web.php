<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes (auth)
|--------------------------------------------------------------------------
| No public self-registration: Super Admin / Admin create every account.
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::redirect('/', '/dashboard');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (any authenticated user manages their own account)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |----------------------------------------------------------------------
    | Inventory - viewable by all 3 roles (policy scopes Assignee to their
    | own assigned assets); create/edit/delete gated to Super Admin + Admin.
    |----------------------------------------------------------------------
    */
    Route::middleware('role:super_admin,admin,assignee')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    });

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('inventory-create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

        // Issue Product
        Route::get('issue', [IssueController::class, 'index'])->name('issue.index');
        Route::get('issue-create', [IssueController::class, 'create'])->name('issue.create');
        Route::post('issue', [IssueController::class, 'store'])->name('issue.store');
        Route::post('issue/transfer', [IssueController::class, 'transfer'])->name('issue.transfer');

        // Return Product
        Route::get('return', [ReturnController::class, 'index'])->name('return.index');
        Route::get('return-create', [ReturnController::class, 'create'])->name('return.create');
        Route::post('return', [ReturnController::class, 'store'])->name('return.store');

        // Maintenance
        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('maintenance-create', [MaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('maintenance/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit');
        Route::put('maintenance/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('maintenance/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{type}', [ReportController::class, 'show'])->name('reports.show');
    });

    /*
    |----------------------------------------------------------------------
    | Super Admin Settings module
    |----------------------------------------------------------------------
    */
      Route::prefix('settings')->name('settings.')->group(function () {

        // User (Assignee) Management - Super Admin + Admin (Admin creates Assignees only)
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users-create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Categories & Locations - Super Admin + Admin
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

            Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
            Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
            Route::put('locations/{location}', [LocationController::class, 'update'])->name('locations.update');
            Route::delete('locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
        });

        // Admin Management, System Settings, Audit Logs - Super Admin ONLY
        Route::middleware('role:super_admin')->group(function () {
            Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
            Route::get('admins-create', [AdminController::class, 'create'])->name('admins.create');
            Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
            Route::get('admins/{admin}/edit', [AdminController::class, 'edit'])->name('admins.edit');
            Route::put('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
            Route::patch('admins/{admin}/change-role', [AdminController::class, 'changeRole'])->name('admins.changeRole');
            Route::patch('admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admins.toggleStatus');

            // System Settings
            Route::get('system', [SystemSettingController::class, 'edit'])->name('system.edit');
            Route::post('system', [SystemSettingController::class, 'update'])->name('system.update');

            // Audit Logs
            Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
        });
    });
});
