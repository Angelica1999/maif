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
        Schema::table('addfacilityinfo', function (Blueprint $table) {
            //
            $table->integer('ewt_pf')->nullable()->after('Ewt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addfacilityinfo', function (Blueprint $table) {
            //
        });
    }
};
