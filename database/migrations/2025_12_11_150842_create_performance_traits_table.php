<?php

use App\Models\PerformanceTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('performance_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_record_id')->constrained('performance_records')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('trait_type', PerformanceTrait::TRAIT_TYPES);
            $table->float('value');
            $table->string('unit');
            $table->string('method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_traits');
    }
};
