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
            $table->string('service_agreement_number')->nullable();
            $table->date('service_agreement_start_date')->nullable();
            $table->date('service_agreement_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('service_agreement_number');
            $table->dropColumn('service_agreement_start_date');
            $table->dropColumn('service_agreement_end_date');
        });
    }
};
