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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->text('base_currency');
            $table->double('unit', 15, 4)->unsigned();
            $table->double('rate', 15, 4)->unsigned();
            $table->text('for_currency');
            $table->date('date');
            $table->timestamps();

            $table->unique(['base_currency', 'for_currency', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
