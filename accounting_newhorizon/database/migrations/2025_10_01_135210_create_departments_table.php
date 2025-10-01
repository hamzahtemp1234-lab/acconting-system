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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete(); // حذف الأقسام عند حذف الفرع

            $table->string('code', 20);                  // رمز القسم (فريد داخل الفرع)
            $table->string('name', 100);                 // اسم القسم

            // مدير القسم (موظف) - nullable لأن الموظفين قد لا يكونوا مضافين بعد
            // $table->foreignId('manager_id')
            //     ->nullable()
            //     ->constrained('employees') // تأكد من وجود جدول employees لاحقًا
            //     ->nullOnDelete()
            //     ->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();

            // فريد مركّب: لا يتكرر نفس كود القسم داخل نفس الفرع
            $table->unique(['branch_id', 'code']);

            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
