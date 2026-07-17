<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pph21_settings', function (Blueprint $table) {
            $table->id();
            $table->year('tax_year');
            $table->decimal('ptkp_tk0', 15, 2);
            $table->decimal('ptkp_tk1', 15, 2);
            $table->decimal('ptkp_tk2', 15, 2);
            $table->decimal('ptkp_tk3', 15, 2);
            $table->decimal('ptkp_k0', 15, 2);
            $table->decimal('ptkp_k1', 15, 2);
            $table->decimal('ptkp_k2', 15, 2);
            $table->decimal('ptkp_k3', 15, 2);
            $table->decimal('tarif_layer1', 5, 2)->default(5);
            $table->decimal('tarif_layer2', 5, 2)->default(15);
            $table->decimal('tarif_layer3', 5, 2)->default(25);
            $table->decimal('tarif_layer4', 5, 2)->default(30);
            $table->decimal('tarif_layer5', 5, 2)->default(35);
            $table->decimal('tarif_batas1', 15, 2)->default(60000000);
            $table->decimal('tarif_batas2', 15, 2)->default(250000000);
            $table->decimal('tarif_batas3', 15, 2)->default(500000000);
            $table->decimal('tarif_batas4', 15, 2)->default(5000000000);
            $table->boolean('dtp_enabled')->default(false);
            $table->decimal('dtp_max_gaji', 15, 2)->nullable();
            $table->decimal('biaya_jabatan_persen', 5, 2)->default(5);
            $table->decimal('biaya_jabatan_max_bulan', 15, 2)->default(500000);
            $table->decimal('biaya_jabatan_max_tahun', 15, 2)->default(6000000);
            $table->decimal('non_npwp_multiplier', 5, 2)->default(1.20);
            $table->boolean('is_active')->default(true);
            $table->date('effective_date');
            $table->timestamps();

            $table->unique('tax_year');
        });

        Schema::create('pph21_ter_rates', function (Blueprint $table) {
            $table->id();
            $table->year('tax_year');
            $table->enum('category', ['A', 'B', 'C']);
            $table->decimal('min_income', 15, 2)->default(0);
            $table->decimal('max_income', 15, 2)->nullable();
            $table->decimal('rate', 5, 2);
            $table->timestamps();

            $table->index(['tax_year', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pph21_ter_rates');
        Schema::dropIfExists('pph21_settings');
    }
};
