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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('TableName', 50);
            $table->unsignedBigInteger('RecordID');
            $table->foreignId('ChangedBy')->constrained('users')->cascadeOnDelete();
            $table->timestamp('ChangeDate')->useCurrent();
            $table->char('ChangeType', 1); // I, U, D
            $table->text('Details')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
