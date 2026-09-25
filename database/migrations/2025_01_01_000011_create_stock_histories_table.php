<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->restrictOnDelete();
            $table->enum('action', ['add', 'issue', 'return', 'transfer', 'adjustment', 'disposal'])->default('add');
            $table->integer('quantity'); // signed: +N for stock in, -N for stock out
            $table->unsignedInteger('balance'); // resulting available_quantity after this movement
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
