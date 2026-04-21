<?php

use App\Enums\HolidayEnum;
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
        Schema::create('holydays', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('name');
            $table->enum('status', HolidayEnum::values());
            $table->timestamps();
            $table->index('date');

            $table->unique(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holydays');
    }
};
