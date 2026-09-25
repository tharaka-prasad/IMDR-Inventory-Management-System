<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            // Identity ------------------------------------------------------
            // Format: {ASSET_PREFIX}-{CATEGORY_CODE}-{SEQUENCE}, e.g. IMDR-IT-001
            $table->string('asset_code')->unique();
            $table->string('qr_code')->unique();
            // Denormalised module flag so Fixed Assets / IT Assets / Consumables
            // screens can filter fast without joining categories.
            $table->enum('asset_type', ['fixed_asset', 'it_asset', 'consumable'])->default('fixed_asset');

            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();

            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->index();

            // Stock ----------------------------------------------------------
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('available_quantity')->default(1);

            // Financials -------------------------------------------------------
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->date('payment_date')->nullable();

            // Warranty ---------------------------------------------------------
            $table->date('warranty_start')->nullable();
            $table->date('warranty_end')->nullable();

            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'disposed'])->default('new');
            $table->enum('status', ['active', 'in_maintenance', 'disposed', 'inactive'])->default('active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'location_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
