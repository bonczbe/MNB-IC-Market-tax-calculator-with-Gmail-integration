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
        Schema::create('forex_events', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->text('name');
            $table->text('previouse')->nullable();
            $table->text('forecast')->nullable();
            $table->timestamps();
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forex_events');
    }
};
