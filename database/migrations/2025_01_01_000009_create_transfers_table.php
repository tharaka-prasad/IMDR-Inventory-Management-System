<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Records a hand-off of an already-issued asset from one assignee to
        // another. The originating assignment is marked "transferred" and a
        // brand-new assignment row is created for the receiving user, so the
        // full chain of custody is always reconstructable and nothing is
        // ever deleted.
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_assignment_id')->constrained('assignments')->restrictOnDelete();
            $table->foreignId('to_assignment_id')->constrained('assignments')->restrictOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories')->restrictOnDelete();
            $table->foreignId('from_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('transfer_date');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
