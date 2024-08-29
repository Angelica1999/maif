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
        Schema::create('new_dv', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('predv_id');
            $table->string('route_no');
            $table->date('date');
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->double('total', 18,2);
            $table->double('accumulated', 15,2);
            $table->integer('dv_no')->nullable();
            $table->integer('ors_no')->nullable();
            $table->string('obligated_by')->nullable();
            $table->string('paid_by')->nullable();
            $table->datetime('obligated_on')->nullable();
            $table->datetime('paid_on')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
