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
        Schema::create('email_extracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_account_id');
            $table->date('date');
            $table->text('content');
            $table->timestamps();

            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts');
            $table->unique(['date', 'broker_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_extracts');
    }
};
