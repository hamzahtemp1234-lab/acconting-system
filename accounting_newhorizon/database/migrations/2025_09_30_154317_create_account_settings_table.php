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
        Schema::create('account_settings', function (Blueprint $table) {

            $table->id();

            // لتجميع الإعداد حسب الوحدة
            $table->string('module', 50); // general, customers, suppliers, sales, purchases, cash, bank, fx ...

            // نوع الإعداد/المفتاح (مثلاً: receivable_account, payable_account, sales_revenue_account ...)
            $table->string('key', 100);

            // ربط بحساب (غالبية الإعدادات حسابات)
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();

            // لو في إعداد نصّي/رقمي غير حساب (خيار احتياطي)
            $table->string('value_string')->nullable();

            // تفعيل/تعطيل
            $table->boolean('is_active')->default(true);

            // نطاق التطبيق (سياق): عام/عملة/تصنيف عملاء ... إلخ
            // scope_type: null (عام) | 'currency' | 'customer_category'
            $table->string('scope_type', 50)->nullable();
            $table->unsignedBigInteger('scope_id')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // منع التكرار لنفس (module, key, scope)
            $table->unique(['module', 'key', 'scope_type', 'scope_id'], 'uniq_mod_key_scope');
            $table->index(['module', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_settings');
    }
};
