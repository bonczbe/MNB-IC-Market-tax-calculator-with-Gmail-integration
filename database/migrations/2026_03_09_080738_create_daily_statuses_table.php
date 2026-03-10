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
        Schema::create('daily_statuses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('currency');
            $table->double('previous_ledger_balance', 15, 2);
            $table->double('balance', 15, 2);
            $table->timestamps();

            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statuses');
    }
};
