<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Short code used as the asset-code segment, e.g. IT, FUR, VEH.
            $table->string('code')->unique();
            // Which module this category belongs to, so Fixed Assets / IT
            // Assets / Consumables can be filtered as separate "modules"
            // while sharing one inventories table.
            $table->enum('asset_type', ['fixed_asset', 'it_asset', 'consumable'])->default('fixed_asset');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
