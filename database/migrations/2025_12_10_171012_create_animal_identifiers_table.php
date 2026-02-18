<?php

use App\Models\AnimalIdentifier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animal_identifiers', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->bigInteger('version')->default(1);


            $table->foreignId('animal_id')->constrained('animals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type', AnimalIdentifier::TYPES);
            $table->string('code');
            $table->boolean('active')->default(true);
            $table->unique(['animal_id', 'type']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_identifiers');
    }
};
