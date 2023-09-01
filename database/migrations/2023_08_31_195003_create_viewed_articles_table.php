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
        Schema::create('viewed_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained(
                table: 'articles',
                column: 'id',
                indexName: 'viewed_articles_article_id'
            );
            $table->foreignId('user_id')->constrained(
                table: 'users',
                column: 'user_id',
                indexName: 'viewed_articles_user_id'
            );
            $table->integer('times_viewed')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewed_articles');
    }
};
