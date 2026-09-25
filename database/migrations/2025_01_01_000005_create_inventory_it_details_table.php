<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_it_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->unique()->constrained('inventories')->cascadeOnDelete();
            $table->string('device_name')->nullable();
            $table->string('hostname')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('gpu')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('monitor_size')->nullable();
            $table->string('printer_type')->nullable();
            $table->string('printer_ip')->nullable();
            $table->text('software_installed')->nullable();
            $table->string('license_reference')->nullable();
            $table->string('antivirus')->nullable();
            $table->text('it_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_it_details');
    }
};
