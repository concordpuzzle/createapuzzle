<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMidjourneyMessageIdToCpImageGenerationsTable extends Migration
{
    public function up()
    {
        Schema::table('cp_image_generations', function (Blueprint $table) {
            $table->string('midjourney_message_id', 40)->nullable();
        });
    }

    public function down()
    {
        Schema::table('cp_image_generations', function (Blueprint $table) {
            $table->dropColumn('midjourney_message_id');
        });
    }
}
