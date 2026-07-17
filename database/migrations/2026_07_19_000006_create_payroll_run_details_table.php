<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_run_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();

            $table->decimal('gross_income', 15, 2)->default(0);

            $table->decimal('bpjs_kes_employee', 15, 2)->default(0);
            $table->decimal('bpjs_kes_employer', 15, 2)->default(0);
            $table->decimal('bpjs_jht_employee', 15, 2)->default(0);
            $table->decimal('bpjs_jht_employer', 15, 2)->default(0);
            $table->decimal('bpjs_jp_employee', 15, 2)->default(0);
            $table->decimal('bpjs_jp_employer', 15, 2)->default(0);
            $table->decimal('bpjs_jkk_employer', 15, 2)->default(0);
            $table->decimal('bpjs_jkm_employer', 15, 2)->default(0);
            $table->decimal('total_bpjs_employee', 15, 2)->default(0);
            $table->decimal('total_bpjs_employer', 15, 2)->default(0);

            $table->decimal('net_income_before_tax', 15, 2)->default(0);
            $table->decimal('pph21_monthly', 15, 2)->default(0);
            $table->decimal('pph21_ter_rate', 5, 2)->nullable();
            $table->string('pph21_method', 30)->nullable();
            $table->decimal('pph21_dtp_amount', 15, 2)->default(0);

            $table->decimal('take_home_pay', 15, 2)->default(0);

            $table->foreignId('calculated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('calculated_at')->nullable();

            $table->json('calculation_detail')->nullable();

            $table->timestamps();

            $table->unique('payroll_item_id');
            $table->index(['payroll_period_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_run_details');
    }
};
