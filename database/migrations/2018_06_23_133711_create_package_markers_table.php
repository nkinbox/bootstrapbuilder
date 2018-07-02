<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_markers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_detail_id');
            $table->string('type', 10); //category, tag, label, inclusions, exclusions, activity, theme
            $table->boolean('primary_marker')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->string('title', 50);
            $table->string('content', 500)->nullable()->default(null);
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->index(['package_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_markers');
    }
}
