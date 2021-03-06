<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('geolocation_id')->default(0);
            $table->string('type', 10); //['attraction', 'landmark', 'locality']
            $table->string('title', 100);
            $table->decimal('latitude', 12, 8)->default(null)->nullable();
            $table->decimal('longitude', 12, 8)->default(null)->nullable();
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->timestamps();
            $table->index('title');
            $table->index(['geolocation_id', 'type', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
