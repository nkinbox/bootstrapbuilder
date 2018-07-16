<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPivotHotelFacility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotel_facilities', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('type'); //hotel allroom roomtype
            $table->dropColumn('content');
            $table->dropColumn('content_id');
            $table->unsignedInteger('data_facility_id')->after('hotel_room_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_facilities', function (Blueprint $table) {
            $table->dropColumn('data_facility_id');
            $table->string('title', 250);
            $table->string('type', 5); //hotel allroom roomtype
            $table->string('content', 500)->nullable()->default(null);
            $table->unsignedInteger('content_id')->default(0);
        });
    }
}
