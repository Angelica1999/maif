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
        Schema::table('proponent_info', function (Blueprint $table) {
            //
            $table->double('in_balance', 20, 2)->nullable()->after('main_proponent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proponent_info', function (Blueprint $table) {
            //
        });
    }
};
