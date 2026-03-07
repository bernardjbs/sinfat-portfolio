<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->enum('type', ['guest', 'admin']);
            $table->text('topic');
            $table->string('model', 100);
            $table->integer('tokens_used')->nullable();
            $table->enum('status', ['pending', 'streaming', 'completed', 'failed']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sessions');
    }
};
