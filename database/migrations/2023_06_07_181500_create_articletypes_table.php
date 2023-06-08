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
        Schema::create('articletypes', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('articletype_id');
            $table->string('name', 60);

            $table->foreignId('subcategory_id')->constrained(
                table: 'subcategories',
                column: 'id',
                indexName: 'articletypes_subcategory_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articletypes');
    }
};
