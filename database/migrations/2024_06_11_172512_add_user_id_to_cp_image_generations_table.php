<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCpImageGenerationsTable extends Migration
{
    public function up()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            if (!Schema::hasColumn('c_p_image_generations', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            } else {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
