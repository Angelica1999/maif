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
        Schema::table('admin_cost_util', function (Blueprint $table){
            Schema::create('admin_cost_util', function (Blueprint $table){
                $table->id();
                $table->integer('util_id')->nullable();
                $table->integer('fundsource_id');
                $table->text('proponent')->nullable();
                $table->text('dv_no')->nullable();
                $table->text('payee')->nullable();
                $table->text('ors_no')->nullable();
                $table->text('recipient')->nullable();
                $table->text('admin_uacs')->nullable();
                $table->double('admin_cost', 20,2);
                $table->integer('created_by');
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
