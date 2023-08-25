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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 10);
            $table->foreignId('user_id')->constrained(
                table: 'users',
                column: 'user_id',
                indexName: 'orders_user_id'
            );
            $table->foreignId('cart_id')->constrained(
                table: 'carts',
                column: 'cart_id',
                indexName: 'orders_cart_id'
            );
            $table->foreignId('address_id')->constrained(
                table: 'addresses',
                column: 'address_id',
                indexName: 'orders_adress_id'
            );
            $table->string('status', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
