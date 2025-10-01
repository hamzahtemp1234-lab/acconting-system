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
        Schema::create('cost_center_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();   // رمز فريد
            $table->string('name', 255);            // اسم النوع
            $table->softDeletes();                  // حذف ناعم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_center_types');
    }
};
