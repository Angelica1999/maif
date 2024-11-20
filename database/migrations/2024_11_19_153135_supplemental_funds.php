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
        Schema::table('supplemental_funds', function (Blueprint $table){
            Schema::create('supplemental_funds', function (Blueprint $table){
                $table->id();
                $table->text('proponent');
                $table->double('amount', 20, 2);
                $table->string('added_by');
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
