<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_usage', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('topic', 500);
            $table->string('model')->nullable();
            $table->boolean('used_own_key')->default(false);
            $table->string('status')->default('streaming');
            $table->unsignedInteger('tokens_used')->nullable();
            $table->timestamps();

            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_usage');
    }
};
