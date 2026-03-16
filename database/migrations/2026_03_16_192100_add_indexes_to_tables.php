<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexek hozzáadása a gyakran szűrt/joinolt oszlopokhoz.
 * A unique constraint-ek automatikusan hoznak létre indexet,
 * ezért csak azokat az oszlopokat indexeljük, amelyeknek
 * nincs unique constraint-je.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->index('date');
        });

        Schema::table('daily_statuses', function (Blueprint $table) {
            $table->index('date');
            $table->index('broker_account_id');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->index('date');
            $table->index('broker_account_id');
        });

        Schema::table('email_extracts', function (Blueprint $table) {
            $table->index('broker_account_id');
        });

        Schema::table('yearly_tax_calculations', function (Blueprint $table) {
            $table->index('broker_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('daily_statuses', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['broker_account_id']);
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['broker_account_id']);
        });

        Schema::table('email_extracts', function (Blueprint $table) {
            $table->dropIndex(['broker_account_id']);
        });

        Schema::table('yearly_tax_calculations', function (Blueprint $table) {
            $table->dropIndex(['broker_account_id']);
        });
    }
};
