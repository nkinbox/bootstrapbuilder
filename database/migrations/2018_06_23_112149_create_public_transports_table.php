<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_transports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('geolocation_id')->default(0);
            $table->string('type', 15); //['airport', 'bus_stand', 'railway_station']
            $table->string('title', 80);
            $table->string('category', 20);
            $table->unsignedDecimal('latitude', 10, 8)->default(null)->nullable();
            $table->unsignedDecimal('longitude', 10, 8)->default(null)->nullable();
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->index(['geolocation_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_transports');
    }
}
