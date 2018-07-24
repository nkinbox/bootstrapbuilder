<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_urls', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->unsignedInteger('page_id');
            $table->unsignedInteger('page_content_id')->default(0);
            $table->string('url', 1000);
            $table->string('geolocation', 45)->nullable()->dafault(null);
            $table->string('regex', 200)->nullable()->dafault(null);
            $table->string('matches', 500)->nullable()->dafault(null);
            $table->unsignedInteger('meta_id')->default(0);
            $table->unsignedInteger('script_id')->default(0);
            $table->unsignedInteger('css_id')->default(0);
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_urls');
    }
}
