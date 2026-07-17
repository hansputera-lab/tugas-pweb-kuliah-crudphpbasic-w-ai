<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_tax_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('npwp', 20)->nullable();
            $table->string('ptkp_status', 10)->default('TK/0');
            $table->enum('tax_method', ['gross', 'gross_up', 'net'])->default('gross');
            $table->string('bpjs_kes_number', 30)->nullable();
            $table->string('bpjs_tk_number', 30)->nullable();
            $table->enum('bpjs_kes_class', ['I', 'II', 'III'])->default('I');
            $table->enum('npwp_status', ['have', 'dont_have'])->default('have');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_tax_status');
    }
};
