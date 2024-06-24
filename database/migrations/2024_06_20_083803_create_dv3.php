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
        Schema::create('dv3', function (Blueprint $table) {
            $table->id();
            $table->text('route_no');
            $table->date('date');
            $table->decimal('total', 15,2);
            $table->integer('facility_id');
            $table->string('obligated_by', 255)->nullable();
            $table->string('dv_no', 255)->nullable();
            $table->string('paid_by', 255)->nullable();
            $table->string('ors_no', 255)->nullable();
            $table->integer('remarks');
            $table->integer('status');
            $table->string('modified_by', 255)->nullable();
            $table->string('created_by', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dv3');
    }
};
