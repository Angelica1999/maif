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
        Schema::table('patients', function (Blueprint $table) {
            //
            $table->string('forwarded_by_p')->nullable()->after('created_by');
            $table->string('retrieved_by_p')->nullable()->after('created_by');
            $table->string('deleted_by')->nullable()->after('created_by');
            $table->string('accepted_by_h')->nullable()->after('created_by');
            $table->string('confirmed_by_h')->nullable()->after('created_by');
            $table->string('returned_by_h')->nullable()->after('created_by');
            $table->string('forwarded_by_m')->nullable()->after('created_by');
            $table->string('returned_by_m')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            //
        });
    }
};
