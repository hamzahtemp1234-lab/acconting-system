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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // مثل: JV, PV, RV
            $table->string('name', 200);          // اسم عربي: قيد يومية / سند صرف / سند قبض...
            $table->enum('module', ['accounting', 'sales', 'purchases', 'general'])->default('accounting');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('requires_approval')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
