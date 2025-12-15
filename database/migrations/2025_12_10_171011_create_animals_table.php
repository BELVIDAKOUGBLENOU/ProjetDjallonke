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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('premises_id')->constrained('premises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('species', ['OVINE', 'CAPRINE']);
            $table->enum('sex', ['M', 'F']);
            $table->date('birth_date')->nullable();
            $table->enum('life_status', ['ALIVE', 'DEAD', 'SOLD']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
