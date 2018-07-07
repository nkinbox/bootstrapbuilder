<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalityToPackageItineraries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_itineraries', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->after('geolocation_id')->default(0);
            $table->dropIndex(['geolocation_id']);
            $table->index(['geolocation_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_itineraries', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->dropIndex(['geolocation_id', 'location_id']);
            $table->index('geolocation_id');
        });
    }
}
