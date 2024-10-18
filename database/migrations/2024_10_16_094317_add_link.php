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
            $table->text('link')->nullable()->after('used');
            $table->text('image')->nullable()->after('used');
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
