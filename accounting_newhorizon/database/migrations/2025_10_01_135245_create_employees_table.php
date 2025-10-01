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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // الأساسيات
            $table->string('code', 20)->unique();    // رقم/رمز الموظف فريد
            $table->string('name', 255);             // الاسم

            // العلاقات
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // إن كان اسم جدول الشجرة لديك مختلفاً، غيّر 'chart_of_accounts' هنا
            $table->foreignId('account_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // بيانات التواصل
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable()->unique();

            $table->timestamps();
            $table->softDeletes();

            // فهارس مساعدة للبحث
            $table->index(['name']);
            $table->index(['phone']);
            // ملاحظة: email عليه unique، والـ NULLات مسموح تكرارها في MySQL.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
