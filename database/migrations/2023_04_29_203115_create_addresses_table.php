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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignId('user_id')->constrained(
                table: 'users',
                column: 'user_id',
                indexName: 'addresses_user_id'
            );
            $table->unsignedTinyInteger('county_id');
            $table->unsignedBigInteger('city_id');
            $table->string('street', 60);
            $table->unsignedInteger('street_number');
            $table->string('building', 10)->nullable();
            $table->string('entrance', 10)->nullable();
            $table->unsignedInteger('apartment')->nullable();
            $table->string('postal_code', 6);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('addresses');
    }
};
