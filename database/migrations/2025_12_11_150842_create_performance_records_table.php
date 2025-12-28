<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('performance_records', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->bigInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('animal_id')->constrained('animals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('recorded_date');
            $table->string('context');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_records');
    }
};
