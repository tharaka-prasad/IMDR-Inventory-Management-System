<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->restrictOnDelete();
            $table->string('issue_title');
            $table->text('description')->nullable();
            $table->string('vendor')->nullable();
            $table->decimal('repair_cost', 14, 2)->default(0);
            $table->date('sent_date')->nullable();
            $table->date('return_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'unrepairable'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
