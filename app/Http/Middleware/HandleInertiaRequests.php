<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'full_name' => $request->user()->full_name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                ] : null,
            ],
            // Institute name + uploaded logo, available on every page
            // (including the login page).
            'branding' => fn () => $this->branding(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }

    private function branding(): array
    {
        $settings = SystemSetting::first();

        return [
            'name' => $settings?->institute_name ?: 'IMDR',
            'logo_url' => $settings?->logo_path
                ? '/storage/' . $settings->logo_path . '?v=' . $settings->updated_at?->timestamp
                : null,
        ];
    }
}