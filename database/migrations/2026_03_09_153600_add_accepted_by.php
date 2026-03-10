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
        Schema::table('transmittal', function (Blueprint $table) {
            //
            $table->string('accepted_by')->nullable()->after('link');
            $table->string('returned_by')->nullable()->after('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transmittal', function (Blueprint $table) {
            //
        });
    }
};
