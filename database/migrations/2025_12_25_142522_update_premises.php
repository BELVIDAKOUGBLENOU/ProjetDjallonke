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
        Schema::table('premises', function (Blueprint $table) {
            if (!Schema::hasColumn('premises', 'uid')) {
                $table->string('uid')->nullable()->after('id')->unique();
            }
        });
        // set uid for existing records
        $premises = \App\Models\Premise::whereNull('uid')->get();
        foreach ($premises as $premise) {
            $premise->uid = \Illuminate\Support\Str::uuid()->toString();
            $premise->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premises', function (Blueprint $table) {
            //
        });
    }
};
