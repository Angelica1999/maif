<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
    $table->id();
    $table->string('user_id')->nullable(); // External DB user reference
    $table->string('email')->index();
    $table->enum('type', ['bug', 'recommendation'])->index();
    $table->text('recommendation');
    $table->enum('status', ['pending', 'approved', 'rejected'])
          ->default('pending')
          ->index();
    $table->text('remarks')->nullable();
    $table->string('evaluated_by')->nullable(); // External evaluator reference
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};