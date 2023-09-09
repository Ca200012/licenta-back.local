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
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('subcategory_id');
            $table->string('name', 60);

            $table->foreignId('category_id')->constrained(
                table: 'categories',
                column: 'id',
                indexName: 'subcategories_category_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
