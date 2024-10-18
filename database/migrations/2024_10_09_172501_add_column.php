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
        Schema::table('utilization', function (Blueprint $table) {
            //
            $table->date('obligated_on')->after('obligated_by')->nullable();
            $table->date('paid_on')->after('paid_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilization', function (Blueprint $table) {
            //
        });
    }
};
