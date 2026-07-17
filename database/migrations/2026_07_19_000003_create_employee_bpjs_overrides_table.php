<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_bpjs_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('component', ['jkk', 'jkm', 'jht', 'jp', 'kes']);
            $table->decimal('rate_employer', 5, 2)->nullable();
            $table->decimal('rate_employee', 5, 2)->nullable();
            $table->decimal('max_wage', 15, 2)->nullable();
            $table->decimal('min_wage', 15, 2)->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'component']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_bpjs_overrides');
    }
};
