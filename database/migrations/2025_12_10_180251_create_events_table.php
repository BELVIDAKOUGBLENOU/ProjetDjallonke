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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->bigInteger('version')->default(1);


            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('animal_id')->constrained('animals')->restrictOnDelete()->cascadeOnUpdate();

            $table->enum('source', ['COMMUNITY_ADMIN', 'FARMER', 'TECHNICIAN']);
            $table->date('event_date');
            $table->text('comment')->nullable();
            $table->boolean('is_confirmed')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
