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
            $table->unsignedInteger("template_id")->default(0);
            $table->unsignedInteger("visibility_id")->default(0);
            $table->unsignedInteger("geolocation")->default(0);
            $table->string("type", 6)->default("body");
            $table->string("name", 50);
            $table->string("category", 10); //["basic", "element", "component", "web"]
            $table->string("node", 6); // ["parent", "self", "child"]
            $table->string("visibility", 5)->default("show"); //["auth", "guest", "show", "none"]
            $table->string("content_type", 8); //["static", "variable", "element"]
            $table->unsignedInteger("child_order")->default(1);
            $table->unsignedInteger("nested_component")->default(null)->nullable();
            $table->string("loop_source", "50")->default(null)->nullable();
            $table->string("start_tag", 10);
            $table->string("end_tag", 10)->default(null)->nullable();
            $table->string("attributes", 500)->default("{}")->nullable();
            $table->string("var_attributes", 500)->default("[]")->nullable();
            $table->string("classes", 500)->default("[]")->nullable();
            $table->string("style", 500)->default("{}")->nullable();
            $table->text("content")->default(null)->nullable();
            $table->index('name');
            $table->index('template_id');
            $table->index('category');
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
