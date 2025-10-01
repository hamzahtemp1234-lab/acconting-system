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
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable();      // تسلسل منفصل لكل فرع إذا رغبت
            $table->unsignedBigInteger('fiscal_year_id')->nullable(); // ربط اختياري بالسنة المالية
            $table->string('prefix', 20)->nullable();                 // مثل: JV-, PV-, RV-
            $table->unsignedInteger('start_number')->default(1);
            $table->unsignedBigInteger('current_number')->default(0);
            $table->unsignedTinyInteger('padding')->default(4);       // عدد أصفار اليسار
            $table->enum('reset_period', ['none', 'year', 'month'])->default('year');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['document_type_id', 'branch_id', 'fiscal_year_id'], 'uniq_seq_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
