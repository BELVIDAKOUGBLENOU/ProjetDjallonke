<?php

use App\Models\Animal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->bigInteger('version')->default(1);

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('premises_id')->constrained('premises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('species', Animal::SPECIES);
            $table->enum('sex', Animal::SEXES);
            $table->date('birth_date')->nullable();
            $table->enum('life_status', Animal::LIFE_STATUSES);

            $table->timestamps();
            $table->softDeletes();
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
