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
        Schema::create('boqs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('work_package_id')->constrained()->cascadeOnDelete();
            $table->string('boq_code');
            $table->string('description');
            $table->string('uom');
            $table->decimal('budget_qty', 10, 2);
            $table->decimal('unit_rate', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boqs');
    }
};
