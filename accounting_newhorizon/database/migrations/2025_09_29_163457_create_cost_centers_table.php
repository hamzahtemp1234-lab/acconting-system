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
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name', 255);
            $table->foreignId('type_id')->constrained('cost_center_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('cost_centers')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->boolean('is_group')->default(false);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();   // ✅ حذف ناعم
            $table->timestamps();

            $table->unique(['code', 'type_id']); // الكود فريد داخل النوع
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};
