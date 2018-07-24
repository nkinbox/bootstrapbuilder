<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropVarAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn("var_attributes");
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
            $table->string("var_attributes", 500)->default("[]")->nullable();
        });
    }
}
