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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();                 // كود الوكيل (AGT-01 ...)
            $table->string('name');                           // اسم الوكيل
            $table->enum('type', ['individual', 'company'])->default('individual'); // فرد/شركة

            // معلومات معرفية
            $table->string('tax_id')->nullable();             // رقم ضريبي
            $table->string('id_number')->nullable();          // هوية/جواز

            // تواصل
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            // ربطات
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();

            // عمولة
            $table->decimal('commission_rate', 5, 2)->default(0); // نسبة % (0-100)

            // حالة وملاحظات
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['name', 'phone', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
