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
        Schema::table('address_infos', function (Blueprint $table) {
            $table->string('fgis_number')->nullable()->change();
            $table->date('fgis_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('address_infos', function (Blueprint $table) {
            $table->string('fgis_number')->nullable(false)->change();
            $table->date('fgis_date')->nullable(false)->change();
        });
    }
};
