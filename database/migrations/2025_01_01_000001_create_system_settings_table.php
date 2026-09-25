<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row settings table (key/value would also work, but a single
        // row is simpler for a settings form with a handful of fixed fields).
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('institute_name')->default('IMDR');
            $table->string('logo_path')->nullable();
            $table->string('asset_prefix')->default('IMDR');
            $table->string('qr_prefix')->default('IMDR-QR');
            $table->string('currency')->default('LKR');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
