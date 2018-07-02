<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_itineraries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_detail_id');
            $table->unsignedInteger('geolocation_id')->default(0);
            $table->unsignedInteger('hotel_id')->default(0);
            $table->string('title', 250);
            $table->unsignedInteger('content_id');
            $table->index('package_detail_id');
            $table->index('geolocation_id');
            $table->index('hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_itineraries');
    }
}
