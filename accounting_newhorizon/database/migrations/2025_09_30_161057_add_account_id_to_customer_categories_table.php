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
        Schema::table('customer_categories', function (Blueprint $table) {
            //
            // حقل الحساب المرتبط بجدول chart_of_accounts
            $table->foreignId('account_id')
                ->nullable()
                ->after('is_active')
                ->constrained('chart_of_accounts')   // اسم جدول الدليل المحاسبي
                ->nullOnDelete();                     // عند حذف الحساب يصبح NULL
            // (بدلاً من cascade حفاظًا على بيانات التصنيف)
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
            //
        });
    }
};
