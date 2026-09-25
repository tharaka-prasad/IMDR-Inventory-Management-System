<?php

use App\Models\Depreciation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep every asset's Net Book Value current automatically (global rule).
Schedule::call(function () {
    Depreciation::with('inventory')->chunk(100, function ($batch) {
        foreach ($batch as $depreciation) {
            $depreciation->recalculate();
        }
    });
})->dailyAt('01:00')->name('recalculate-depreciation');
