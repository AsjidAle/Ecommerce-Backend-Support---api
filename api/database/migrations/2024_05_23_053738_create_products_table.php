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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('stock');
            $table->unsignedBigInteger('subCategory');
            $table->unsignedBigInteger('brand');
            $table->foreign('brand')->references('id')->on('brands');
            $table->foreign('subCategory')->references('id')->on('sub_categories');
            $table->string('source')->nullable();
            $table->unsignedBigInteger('sourcePrice');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
