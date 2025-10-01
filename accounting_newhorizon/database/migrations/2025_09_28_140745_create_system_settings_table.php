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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('fiscal_start_month')->unsigned()->default(1);
            $table->unsignedBigInteger('default_currency_id')->nullable();
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->json('extra')->nullable();
            $table->timestamps();
            $table->softDeletes(); // added soft delete
            // Foreign key relation to currencies
            $table->foreign('default_currency_id')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
