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
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('product');
            $table->unsignedBigInteger('qty');
            $table->unsignedBigInteger('price');
            $table->string('state');
            $table->string('city');
            $table->text('address');
            $table->foreign('product')->references('id')->on('products')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user')->references('id')->on('users');
            $table->enum('status', ['PENDING', 'PROCESSING', 'FULFILED', 'DELIVERED'])->default('PENDING');
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
