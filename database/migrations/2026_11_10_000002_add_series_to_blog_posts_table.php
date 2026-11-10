<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('series_id')->nullable()->after('category');
            $table->unsignedInteger('series_part')->nullable()->after('series_id');

            $table->unique(['series_id', 'series_part']);
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropUnique(['series_id', 'series_part']);
            $table->dropColumn(['series_id', 'series_part']);
        });
    }
};
