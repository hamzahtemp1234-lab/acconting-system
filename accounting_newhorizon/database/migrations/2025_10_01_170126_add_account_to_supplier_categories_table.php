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
        Schema::table('suplier_categories', function (Blueprint $table) {
            // حقل الحساب المرتبط بجدول chart_of_accounts
            $table->foreignId('account_id')
                ->nullable()
                ->after('is_active')
                ->constrained('chart_of_accounts') // اسم جدول الدليل المحاسبي
                ->nullOnDelete();                  // عند حذف الحساب يصبح NULL

            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suplier_categories', function (Blueprint $table) {
            // يسقط المفتاح الأجنبي والعمود معًا (لو متاح في إصدارك)
            $table->dropConstrainedForeignId('account_id');

            // إن كان إصدار لارافيلك لا يدعم dropConstrainedForeignId:
            // $table->dropForeign(['account_id']);
            // $table->dropColumn('account_id');
        });
    }
};
