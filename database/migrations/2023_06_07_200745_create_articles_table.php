<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('article_id');
            $table->string('size_0', 5);
            $table->string('size_1', 5);
            $table->string('size_2', 5);
            $table->string('size_3', 5);
            $table->string('size_4', 5);
            $table->integer('price');
            $table->integer('discounted_price');
            $table->string('article_number', 60);
            $table->text('display_name');
            $table->string('brand_name', 60);
            $table->string('colour', 60);
            $table->string('season', 60);
            $table->string('usage', 60);
            $table->string('pattern', 60);
            $table->string('first_image', 60);
            $table->string('second_image', 60);
            $table->string('third_image', 60);
            $table->text('description');

            $table->foreignId('gender_id')->constrained(
                table: 'genders',
                column: 'gender_id',
                indexName: 'articles_gender_id'
            );
            $table->foreignId('category_id')->constrained(
                table: 'categories',
                column: 'id',
                indexName: 'articles_category_id'
            );
            $table->foreignId('subcategory_id')->constrained(
                table: 'subcategories',
                column: 'id',
                indexName: 'articles_subcategory_id'
            );
            $table->foreignId('articletype_id')->constrained(
                table: 'articletypes',
                column: 'id',
                indexName: 'articles_articletype_id'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
