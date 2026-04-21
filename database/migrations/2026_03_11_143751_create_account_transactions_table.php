<?php

use App\Enums\AccountTransactionTypeEnum;
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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_account_id');
            $table->date('date');
            $table->enum('type', AccountTransactionTypeEnum::values());
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('broker_account_id')
                ->references('id')->on('broker_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
