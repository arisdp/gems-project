<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('progress_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('boq_id')->constrained()->cascadeOnDelete();
            $table->date('progress_date');
            $table->decimal('actual_qty', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_entries');
    }
};
