<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('components', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("geolocation")->default(0);
            $table->string("name", 50);
            $table->enum("category",["basic", "element", "component"]);
            $table->enum("node", ["parent", "self", "child"]);
            $table->enum("visibility", ["auth", "guest", "show", "none"])->default("show");
            $table->enum("content_type", ["static", "variable", "element"]);
            $table->unsignedInteger("child_order")->default(1);
            $table->unsignedInteger("nested_component")->default(null)->nullable();
            $table->string("loop_source", "200")->default(null)->nullable();
            $table->string("start_tag", 10);
            $table->string("end_tag", 10)->default(null)->nullable();
            $table->string("attributes", 500)->default("{}")->nullable();
            $table->string("var_attributes", 500)->default("[]")->nullable();
            $table->string("classes", 500)->default("[]")->nullable();
            $table->string("style", 500)->default("{}")->nullable();
            $table->string("content", 1000)->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('components');
    }
}
