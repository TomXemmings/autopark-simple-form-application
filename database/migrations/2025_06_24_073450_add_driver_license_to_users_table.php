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
        Schema::table('users', function (Blueprint $table) {
            $table->string('driver_license_number')->nullable();
            $table->date('driver_license_start_date')->nullable();
            $table->date('driver_license_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('driver_license_number');
            $table->dropColumn('driver_license_start_date');
            $table->dropColumn('driver_license_end_date');
        });
    }
};
