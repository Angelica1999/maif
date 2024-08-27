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
        Schema::create('pre_dv_control', function (Blueprint $table){
            $table->id();
            $table->integer('predv_extension_id');
            $table->text('control_no');
            $table->string('patient_1');
            $table->string('patient_2');
            $table->double('amount', 15,3);
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
