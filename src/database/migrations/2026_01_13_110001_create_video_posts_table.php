<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Создание таблицы video_posts для хранения видео постов
     */
    public function up(): void
    {
        Schema::create('video_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('Название видео поста');
            $table->text('description')->comment('Описание видео поста');
            $table->timestamps();

            // Индексы для оптимизации поиска
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_posts');
    }
};
