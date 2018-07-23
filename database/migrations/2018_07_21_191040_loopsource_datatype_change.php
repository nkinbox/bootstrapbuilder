<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LoopsourceDatatypeChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('loop_source');
            $table->dropColumn('geolocation');
        });
        Schema::table('components', function (Blueprint $table) {
            $table->unsignedInteger('loop_source')->after('nested_component')->nullable()->default(null);
            $table->string("geolocation", "250")->after('visibility_id')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('loop_source');
            $table->dropColumn('geolocation');
        });
        Schema::table('components', function (Blueprint $table) {
            $table->string("loop_source", "50")->after('nested_component')->default(null)->nullable();
            $table->unsignedInteger("geolocation")->default(0)->after('visibility_id');
        });
    }
}
