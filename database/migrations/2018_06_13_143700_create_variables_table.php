<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id'); //not to dump in Client
            $table->string('variable_name', 100);
            $table->boolean('is_php')->default(0);
            $table->string('evaluate', 200)->nullable()->default(null);
            $table->text("php_code")->nullable()->default(null);
            $table->index(['template_id', 'variable_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variables');
    }
}
