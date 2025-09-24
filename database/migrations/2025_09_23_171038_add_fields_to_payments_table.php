<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_for')->nullable()->after('pay_status');   // rent, utilities, etc.
            $table->string('account_number')->nullable()->after('payment_for'); // for online banking
            $table->string('proof')->nullable()->after('account_number');       // path to screenshot
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_for', 'account_number', 'proof']);
        });
    }
};
