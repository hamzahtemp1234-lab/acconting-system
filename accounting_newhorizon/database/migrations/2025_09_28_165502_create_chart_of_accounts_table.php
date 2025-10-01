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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();

            // التدرج الشجري
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');

            // البيانات الأساسية
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // الخصائص المحاسبية
            $table->foreignId('account_type_id')->constrained('account_types')->onDelete('restrict');
            $table->enum('nature', ['مدين', 'دائن']);
            $table->boolean('is_group')->default(false); // هل هو حساب تجميعي
            $table->unsignedTinyInteger('level')->default(1); // مستوى الحساب

            // إعدادات إضافية
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->boolean('allow_entry')->default(true); // هل يقبل القيود
            $table->boolean('is_default')->default(false);
            $table->enum('status', ['نشط', 'غير نشط'])->default('نشط');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
