<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('competency');
            $table->decimal('target_value', 10, 2)->nullable();
            $table->integer('weight')->default(0);
            $table->string('measurement_unit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('period');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->decimal('total_score', 5, 2)->default(0);
            $table->string('final_grade')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period']);
        });

        Schema::create('appraisal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained('appraisals')->cascadeOnDelete();
            $table->foreignId('kpi_id')->constrained('kpis')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->integer('weight')->default(0);
            $table->text('achievement')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('feedback_360', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained('appraisals')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reviewer_name');
            $table->enum('relationship', ['manager', 'peer', 'subordinate', 'self'])->default('peer');
            $table->decimal('rating', 5, 2)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_360');
        Schema::dropIfExists('appraisal_details');
        Schema::dropIfExists('appraisals');
        Schema::dropIfExists('kpis');
    }
};
