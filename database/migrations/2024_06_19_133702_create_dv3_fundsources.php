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
        Schema::create('dv3_fundsources', function (Blueprint $table) {
            $table->id();
            $table->string('route_no', 255);
            $table->integer('fundsource_id');
            $table->integer('info_id');
            $table->decimal('amount', 15,2);
            $table->decimal('vat', 10,2);
            $table->decimal('ewt', 10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dv3_fundsources');
    }
};
