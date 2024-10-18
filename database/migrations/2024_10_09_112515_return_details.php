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
        Schema::table('return_details', function (Blueprint $table){
            Schema::create('return_details', function (Blueprint $table){
                $table->id();
                $table->integer('transmittal_id');
                $table->integer('ref_id');
                $table->text('remarks');
                $table->string('returned_by');
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
