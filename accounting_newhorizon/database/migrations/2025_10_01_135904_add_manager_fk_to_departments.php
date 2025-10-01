<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // 1) إضافة العمود إن لم يكن موجودًا
            if (!Schema::hasColumn('departments', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')
                    ->nullable()
                    ->after('name'); // ضع العمود بعد "name" للوضوح
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            // 2) إضافة القيد الأجنبي بعد التأكد من وجود employees
            $table->foreign('manager_id', 'departments_manager_id_foreign')
                ->references('id')->on('employees')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // أسقط القيد ثم العمود
            if (Schema::hasColumn('departments', 'manager_id')) {
                $table->dropForeign('departments_manager_id_foreign');
                $table->dropColumn('manager_id');
            }
        });
    }
};
