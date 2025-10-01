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
        Schema::table('chart_of_accounts', function (Blueprint $table) {

            // تعديل نوع الحساب ليكون nullable
            $table->unsignedBigInteger('account_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {

            // نرجع account_type_id كما كان (غير nullable)
            $table->unsignedBigInteger('account_type_id')->nullable(false)->change();
        });
    }
};
