<?php

use App\Enums\CountryEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forex_events', function (Blueprint $table) {
            $table->enum('country', CountryEnum::values())->default(CountryEnum::US->value)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forex_events', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
