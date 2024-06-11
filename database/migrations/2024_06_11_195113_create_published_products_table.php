<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_published_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishedProductsTable extends Migration
{
    public function up()
    {
        Schema::create('published_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('image_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('product_id');
            $table->string('product_url');
            $table->string('cropped_image')->nullable();
            $table->timestamps();

            $table->foreign('image_id')->references('id')->on('c_p_image_generations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('published_products');
    }
}
