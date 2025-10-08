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
        Schema::table('tenant_applications', function (Blueprint $table) {
            $table->string('room_no')->nullable()->after('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_applications', function (Blueprint $table) {
            $table->dropColumn('room_no');
        });
    }

};
