<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLikesCountToPublishedProductsTable extends Migration
{
    public function up()
    {
        Schema::table('published_products', function (Blueprint $table) {
            $table->integer('likes_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('published_products', function (Blueprint $table) {
            $table->dropColumn('likes_count');
        });
    }
}
