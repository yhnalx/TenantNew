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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            // Lease details
            $table->string('room_number')->nullable(); // in case property has rooms
            $table->date('lea_start_date');
            $table->date('lea_end_date');
            $table->string('lea_status')->default('active'); // active, ended
            $table->string('lea_terms')->nullable();

            // Renewal request
            $table->boolean('renewal_requested')->default(false);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
