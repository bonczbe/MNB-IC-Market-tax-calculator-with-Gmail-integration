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
        Schema::create('yearly_tax_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_account_id');
            $table->year('tax_year');
            $table->decimal('gross_profit', 15, 2);    // bruttó nyereség az évben
            $table->decimal('loss_carried_forward', 15, 2)->default(0); // felhasznált korábbi veszteség
            $table->decimal('taxable_income', 15, 2);  // adóalap
            $table->decimal('tax_amount', 15, 2);       // 15% adó
            $table->decimal('unused_loss', 15, 2)->default(0);  // fel nem használt veszteség görgetése, fel nem használt összegek öszessége (utolsó 2 éve)
            $table->timestamps();

            $table->foreign('broker_account_id')
                ->references('id')->on('broker_accounts');
            $table->unique(['broker_account_id', 'tax_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yearly_tax_calculations');
    }
};
