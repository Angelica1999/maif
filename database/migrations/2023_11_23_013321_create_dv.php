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
        Schema::create('dv', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('payee');
            $table->string('address');
            $table->dateTime('month_year_from');
            $table->dateTime('month_year_to');
            $table->string('saa_number')->nullable();
            $table->double('amount1')->nullable();
            $table->double('amount2')->nullable();
            $table->double('amount3')->nullable();
            $table->decimal('total_amount', 20, 2)->nullable();
            $table->string('deduction1')->nullable();
            $table->string('deduction2')->nullable();
            $table->decimal('deduction_amount1', 20, 2)->nullable();
            $table->decimal('deduction_amount2', 20, 2)->nullable();
            $table->decimal('total_deduction_amount', 5, 2)->nullable();
            $table->decimal('overall_total_amount', 20, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dv');
    }
};
