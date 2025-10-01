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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();        // رمز فرع فريد
            $table->string('name', 100);                  // اسم الفرع
            $table->string('address', 255)->nullable();   // العنوان
            $table->string('phone', 50)->nullable();      // الهاتف
            $table->boolean('is_active')->default(true);  // مفعّل
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
