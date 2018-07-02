<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('geolocation_id')->default(0);
            $table->boolean('visibility')->default(true);
            $table->string('hotel_name', 100);
            $table->string('address', 100)->default(null)->nullable();
            $table->unsignedInteger('no_of_rooms')->default(null)->nullable();
            $table->unsignedInteger('meta_id')->default(0);
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('policy_id')->default(0);
            $table->unsignedDecimal('latitude', 10, 8)->default(null)->nullable();
            $table->unsignedDecimal('longitude', 10, 8)->default(null)->nullable();
            $table->unsignedInteger('user_id')->default(0);
            $table->timestamps();
            $table->index('hotel_name');
            $table->index(['geolocation_id', 'visibility', 'hotel_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotels');
    }
}
