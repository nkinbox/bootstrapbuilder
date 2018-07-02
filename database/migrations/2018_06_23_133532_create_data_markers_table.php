<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_markers', function (Blueprint $table) {
            $table->increments('id');
            $table->char('category',5); //hotel, packg
            $table->string('type', 10); //category, tag, label, inclusions, exclusions, activity, theme
            $table->string('title', 50);
            $table->string('content', 500)->nullable()->default(null);
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_markers');
    }
}
