<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('imap_host')->default('imap.gmail.com');
            $table->integer('imap_port')->default(993);
            $table->string('imap_encryption')->default('ssl');
            $table->boolean('imap_validate_cert')->default(true);
            $table->string('imap_username')->default('change-me@change.me');
            $table->string('imap_password')->default(Crypt::encryptString('change-me'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('imap_host');
            $table->dropColumn('imap_port');
            $table->dropColumn('imap_encryption');
            $table->dropColumn('imap_validate_cert');
            $table->dropColumn('imap_username');
            $table->dropColumn('imap_password');
        });
    }
};
