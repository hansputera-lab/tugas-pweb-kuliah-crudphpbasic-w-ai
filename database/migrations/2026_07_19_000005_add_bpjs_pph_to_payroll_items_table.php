<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->decimal('total_bpjs_employee', 15, 2)->default(0)->after('total_deduction');
            $table->decimal('pph21_amount', 15, 2)->default(0)->after('total_bpjs_employee');
            $table->decimal('take_home_pay', 15, 2)->default(0)->after('pph21_amount');
            $table->decimal('company_cost', 15, 2)->default(0)->after('take_home_pay');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn(['total_bpjs_employee', 'pph21_amount', 'take_home_pay', 'company_cost']);
        });
    }
};
