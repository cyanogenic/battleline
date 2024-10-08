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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['troop', 'tactic']);
            $table->unsignedTinyInteger('value')->nullable(); // 对于部队卡：1-10
            $table->string('color')->nullable(); // 部队卡颜色
            $table->timestamps();
        });

        Schema::create('flag_card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battlefield_id')->constrained('flags')->onDelete('cascade');
            $table->foreignId('card_id')->constrained('cards');
            $table->foreignId('player_id')->constrained('users');
            $table->unsignedTinyInteger('index'); // 0 到 2
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(['cards', 'flag_card']);
    }
};
