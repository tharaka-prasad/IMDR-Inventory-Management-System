<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('return_date');
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->enum('condition', ['new', 'good', 'fair', 'damaged'])->default('good');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
