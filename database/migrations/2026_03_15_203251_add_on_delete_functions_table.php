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
        Schema::table('email_extracts', function (Blueprint $table) {
            $table->dropForeign(['broker_account_id']);
            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts')
                ->onDelete('cascade');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign(['broker_account_id']);
            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts')
                ->onDelete('cascade');
        });

        Schema::table('yearly_tax_calculations', function (Blueprint $table) {
            $table->dropForeign(['broker_account_id']);
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
        Schema::table('email_extracts', function (Blueprint $table) {
            $table->dropForeign(['broker_account_id']);
            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign(['broker_account_id']);
            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts');
        });

        Schema::table('yearly_tax_calculations', function (Blueprint $table) {
            $table->dropForeign(['broker_account_id']);
            $table->foreign('broker_account_id')
                ->references('id')
                ->on('broker_accounts');
        });
    }
};
