<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('component', ['jkk', 'jkm', 'jht', 'jp', 'kes']);
            $table->decimal('rate_employer', 5, 2);
            $table->decimal('rate_employee', 5, 2)->default(0);
            $table->decimal('max_wage', 15, 2)->nullable();
            $table->decimal('min_wage', 15, 2)->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->nullable();
            $table->decimal('risk_rate', 5, 2)->nullable();
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_settings');
    }
};
