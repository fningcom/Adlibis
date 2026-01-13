<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Создание таблицы comments для хранения комментариев
     * с поддержкой polymorphic отношений и вложенной структуры
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content')->comment('Текст комментария');
            
            // Связь с пользователем (автором)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('ID автора комментария');
            
            // Polymorphic отношение (к News или VideoPost)
            $table->string('commentable_type')->comment('Тип сущности (News, VideoPost)');
            $table->unsignedBigInteger('commentable_id')->comment('ID сущности');
            
            // Поддержка вложенности комментариев
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('comments')
                  ->onDelete('cascade')
                  ->comment('ID родительского комментария для вложенных ответов');
            
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index(['commentable_type', 'commentable_id'], 'comments_commentable_index');
            $table->index('parent_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
