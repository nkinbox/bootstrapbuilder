<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalityToHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->after('visibility')->default(0);
            $table->dropIndex(['geolocation_id', 'visibility', 'hotel_name']);
            $table->index(['geolocation_id', 'visibility', 'location_id', 'hotel_name']);
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
            $table->dropColumn('location_id');
            $table->dropIndex(['geolocation_id', 'visibility', 'location_id', 'hotel_name']);
            $table->index(['geolocation_id', 'visibility', 'hotel_name']);
        });
    }
}
