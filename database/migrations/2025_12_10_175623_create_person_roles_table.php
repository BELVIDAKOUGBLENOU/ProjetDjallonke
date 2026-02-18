<?php

use App\Models\PersonRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('person_roles', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->bigInteger('version')->default(1);

            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('animal_id')->constrained('animals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('role_type', PersonRole::ROLE_TYPES);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_roles');
    }
};
