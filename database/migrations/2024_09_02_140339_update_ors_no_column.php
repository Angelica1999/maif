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
        Schema::table('new_dv', function (Blueprint $table) {
            //
            $table->string('ors_no')->nullable()->change();
            $table->string('dv_no')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_dv', function (Blueprint $table) {
            //
        });
    }
};
