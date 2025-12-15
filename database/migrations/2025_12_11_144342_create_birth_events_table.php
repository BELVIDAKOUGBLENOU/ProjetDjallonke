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
        Schema::create('birth_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('mother_id')->nullable()->constrained('animals')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('father_id')->nullable()->constrained('animals')->restrictOnDelete()->cascadeOnUpdate();
            $table->integer('nb_alive');
            $table->integer('nb_dead');
            $table->unique('event_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birth_events');
    }
};
