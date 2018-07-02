<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('continent', 15);
            $table->string('country', 45);
            $table->string('division', 50)->default('');
            $table->string('subdivision', 50)->default('');
            $table->string('city', 50)->default('');
            $table->string('time_zone', 50)->default('');
            $table->boolean('is_in_european_union')->default(0);
            $table->index(['continent', 'country', 'division', 'subdivision', 'city']);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_locations');
    }
}
