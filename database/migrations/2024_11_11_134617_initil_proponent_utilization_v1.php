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
        //
        Schema::table('pro_utilization_v1', function(Blueprint $table){
            Schema::create('pro_utilization_v1', function (Blueprint $table){
                $table->id();
                $table->integer('proponent_id');
                $table->string('proponent_code');
                $table->integer('patient_id');
                $table->double('amount', 20,2);
                $table->integer('status')->nullable();
                $table->timestamps();
            });
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
