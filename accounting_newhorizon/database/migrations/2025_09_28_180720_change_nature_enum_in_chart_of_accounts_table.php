<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ غيّر العمود مؤقتاً إلى VARCHAR
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            DB::statement("ALTER TABLE chart_of_accounts MODIFY COLUMN nature VARCHAR(20) NULL");
        });

        // 2️⃣ حوّل البيانات
        DB::table('chart_of_accounts')
            ->where('nature', 'مدين')
            ->update(['nature' => 'debit']);

        DB::table('chart_of_accounts')
            ->where('nature', 'دائن')
            ->update(['nature' => 'credit']);

        // 3️⃣ أعد تقييد العمود كـ ENUM
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            DB::statement("ALTER TABLE chart_of_accounts MODIFY COLUMN nature ENUM('debit','credit') NOT NULL");
        });
    }

    public function down(): void
    {
        // عكس العملية

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            DB::statement("ALTER TABLE chart_of_accounts MODIFY COLUMN nature VARCHAR(20) NULL");
        });

        DB::table('chart_of_accounts')
            ->where('nature', 'debit')
            ->update(['nature' => 'مدين']);

        DB::table('chart_of_accounts')
            ->where('nature', 'credit')
            ->update(['nature' => 'دائن']);

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            DB::statement("ALTER TABLE chart_of_accounts MODIFY COLUMN nature ENUM('مدين','دائن') NOT NULL");
        });
    }
};
