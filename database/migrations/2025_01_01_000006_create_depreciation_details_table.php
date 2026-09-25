<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->unique()->constrained('inventories')->cascadeOnDelete();
            // SLM = Straight Line Method, WDV = Written Down Value
            $table->enum('method', ['SLM', 'WDV'])->default('SLM');
            $table->unsignedInteger('useful_life')->nullable(); // in years
            $table->decimal('accumulated_depreciation', 14, 2)->default(0);
            $table->decimal('net_book_value', 14, 2)->default(0);
            $table->date('disposal_date')->nullable();
            $table->string('disposal_reason')->nullable();
            $table->decimal('disposal_value', 14, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciation_details');
    }
};
