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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود العميل
            $table->string('name'); // اسم العميل
            $table->enum('type', ['individual', 'company'])->default('individual'); // نوع العميل
            $table->string('tax_id')->nullable(); // الرقم الضريبي
            $table->string('id_number')->nullable(); // الهوية / الجواز
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('customer_categories')->nullOnDelete();

            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->date('opening_balance_date')->nullable();

            $table->string('payment_terms')->nullable();
            $table->enum('preferred_payment_method', ['cash', 'bank', 'cheque', 'card'])->nullable();

            $table->boolean('is_active')->default(true);
            $table->date('registration_date')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
