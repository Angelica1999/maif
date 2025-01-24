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
        Schema::table('fundsource', function (Blueprint $table) {
            //
            $table->string('added_by')->nullable()->after('cost_value');
            $table->double('budget_cost', 15,2)->nullable()->after('cost_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fundsource', function (Blueprint $table) {
            //
        });
    }
};
