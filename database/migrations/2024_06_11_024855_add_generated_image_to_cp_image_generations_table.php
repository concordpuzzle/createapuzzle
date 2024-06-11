<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneratedImageToCpImageGenerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->string('generated_image')->after('prompt'); // Adjust the position as needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->dropColumn('generated_image');
        });
    }
}
