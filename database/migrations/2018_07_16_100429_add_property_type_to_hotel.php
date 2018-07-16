<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPropertyTypeToHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('property_type', 10)->after('hotel_name');
            $table->dropIndex(['geolocation_id', 'visibility', 'location_id', 'hotel_name']);
            $table->index(['geolocation_id', 'visibility', 'location_id', 'hotel_name', 'property_type'], "search_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex('search_index');
            $table->index(['geolocation_id', 'visibility', 'location_id', 'hotel_name']);
            $table->dropColumn('property_type');
        });
    }
}
