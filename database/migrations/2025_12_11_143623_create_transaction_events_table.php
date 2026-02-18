<?php

use App\Models\TransactionEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('transaction_type', TransactionEvent::TRANSACTION_TYPES);
            $table->decimal('price', 10, 2);
            $table->foreignId('buyer_id')->constrained('persons')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('seller_id')->constrained('persons')->restrictOnDelete()->cascadeOnUpdate();
            $table->unique('event_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_events');
    }
};
