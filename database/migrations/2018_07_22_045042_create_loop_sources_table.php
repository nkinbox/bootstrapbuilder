<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoopSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loop_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('database_variables', 50)->nullable()->default(null);
            $table->string('object_query', 200)->nullable()->default(null);
            $table->string('property_query', 200)->nullable()->default(null);
            $table->string('variables', 500)->nullable()->default(null);
            $table->boolean('relation')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loop_sources');
    }
}
