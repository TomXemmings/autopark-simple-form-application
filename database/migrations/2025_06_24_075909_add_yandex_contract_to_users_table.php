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
            $table->string('yandex_contract_number')->nullable();
            $table->date('yandex_contract_start_date')->nullable();
            $table->string('yandex_contract_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('yandex_contract_number');
            $table->dropColumn('yandex_contract_start_date');
            $table->dropColumn('yandex_contract_file');
        });
    }
};
