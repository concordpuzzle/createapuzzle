<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->string('image_type')->default('original'); // New column with default value 'original'
        });
    }
    
    public function down()
    {
        Schema::table('c_p_image_generations', function (Blueprint $table) {
            $table->dropColumn('image_type');
        });
    }
    
};
