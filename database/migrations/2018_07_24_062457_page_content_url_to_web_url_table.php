<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PageContentUrlToWebUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_contents', function (Blueprint $table) {
            $table->dropColumn('geolocation_id');
            $table->dropColumn('url');
            $table->unsignedInteger('web_url_id')->after('page_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_contents', function (Blueprint $table) {
            $table->unsignedInteger('geolocation_id')->default(0)->index();
            $table->string('url', 500)->nullable()->default(null);
            $table->dropColumn('web_url_id');
        });
    }
}
