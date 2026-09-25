<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSystemSettingRequest;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $settings = SystemSetting::current();
        $this->authorize('view', $settings);

        return Inertia::render('Settings/System/Index', ['settings' => $settings]);
    }

    public function update(UpdateSystemSettingRequest $request)
    {
        $settings = SystemSetting::current();
        $this->authorize('update', $settings);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        unset($data['logo']);

        $settings->update($data);

        return back()->with('success', 'System settings updated.');
    }
}
