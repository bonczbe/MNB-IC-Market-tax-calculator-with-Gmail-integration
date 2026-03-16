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
        Schema::table('rates', function (Blueprint $table) {
            $table->decimal('unit', 15, 4)->unsigned()->change();
            $table->decimal('rate', 15, 4)->unsigned()->change();
        });

        Schema::table('daily_statuses', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->double('unit', 15, 4)->unsigned()->change();
            $table->double('rate', 15, 4)->unsigned()->change();
        });

        Schema::table('daily_statuses', function (Blueprint $table) {
            $table->double('balance', 15, 2)->change();
        });
    }
};
