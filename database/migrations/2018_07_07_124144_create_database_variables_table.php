<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object', 50); //Model Name
            $table->string('property', 50); //Column Name
            $table->boolean('is_array')->default(false); //hasMany
            $table->boolean('related')->default(false);
            $table->unsignedInteger('related_to')->default(0); //ObjectID
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('database_variables');
    }
}
