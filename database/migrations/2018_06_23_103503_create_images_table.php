<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('visibility')->default(true);
            $table->string('type', 7); //['hotel', 'package', 'asset']
            $table->unsignedInteger('belongs_to');
            $table->string('image_title', 60);
            $table->string('file_name', 100);
            $table->unsignedInteger('user_id')->default(0);
            $table->timestamps();
            $table->index(['visibility', 'type', 'belongs_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
