<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCpImageGenerationsTable extends Migration
{
    public function up()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
