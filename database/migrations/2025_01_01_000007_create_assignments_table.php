<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->restrictOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('issue_date');
            $table->foreignId('given_by')->constrained('users')->restrictOnDelete();
            $table->text('remarks')->nullable();
            // issued -> fully with assignee | partially_returned | returned | transferred
            $table->enum('status', ['issued', 'partially_returned', 'returned', 'transferred'])->default('issued');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            // Assignment history must never be hard-deleted.
            $table->softDeletes();

            $table->index(['assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
