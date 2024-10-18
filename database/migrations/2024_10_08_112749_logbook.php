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
        Schema::table('logbook', function (Blueprint $table){
            Schema::create('logbook', function (Blueprint $table){
                $table->id();
                $table->string('control_no');
                $table->date('received_on');
                $table->string('received_by');
                $table->string('delivered_by');
                $table->text('remarks')->nullable();
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
