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
        Schema::table('dv3', function (Blueprint $table) {
            //
            $table->date('obligated_on')->nullable()->after('obligated_by');
            $table->date('paid_on')->nullable()->after('paid_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dv3', function (Blueprint $table) {
            //
        });
    }
};
