<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id')->default(0);
            $table->unsignedInteger('page_id')->default(0);
            $table->boolean('broked')->default(false);
            $table->string('type', 10)->default('sitemap');
            $table->string('group_title', 250)->nullable()->default(null);
            $table->string('title', 250);
            $table->string('url', 500)->nullable()->default(null);
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
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
        Schema::dropIfExists('page_contents');
    }
}
