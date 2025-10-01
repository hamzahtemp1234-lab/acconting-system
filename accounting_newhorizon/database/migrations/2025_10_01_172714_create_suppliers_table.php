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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            // الأساسيات
            $table->string('code', 20)->unique();     // رمز المورد (فريد)
            $table->string('name', 255);              // اسم المورد
            $table->string('phone', 50)->nullable();  // الهاتف
            $table->string('email', 100)->nullable(); // البريد

            // العلاقات
            $table->foreignId('account_id')
                ->nullable()
                ->constrained('chart_of_accounts')    // ربط بالحساب في دليل الحسابات
                ->nullOnDelete();                     // عند حذف الحساب يصبح NULL

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('suplier_categories')  // تصنيف المورد
                ->nullOnDelete();                     // عند حذف التصنيف يصبح NULL

            // الحالة
            $table->boolean('is_active')->default(true);

            // التواريخ
            $table->softDeletes();
            $table->timestamps();

            // فهارس مساعدة
            $table->index(['name']);
            $table->index(['phone']);
            $table->index(['email']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
