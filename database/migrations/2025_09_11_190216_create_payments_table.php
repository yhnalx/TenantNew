<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('tenant_id')
                ->constrained('users') // 👈 reference users table instead of tenants
                ->onDelete('cascade');

            $table->foreignId('lease_id')->constrained()->onDelete('cascade');

            // Payment details
            $table->date('pay_date');
            $table->decimal('pay_amount', 10, 2);
            $table->string('pay_method'); // Cash, Bank Transfer, etc.
            $table->string('pay_status'); // Paid, Pending, Overdue

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
