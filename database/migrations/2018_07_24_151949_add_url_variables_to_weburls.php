<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlVariablesToWeburls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_urls', function (Blueprint $table) {
            $table->string('url_builder', 500)->after('matches')->nullable()->default(null);
            $table->string('url_variables', 500)->after('matches')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_urls', function (Blueprint $table) {
            $table->dropColumn('url_variables');
            $table->dropColumn('url_builder');
        });
    }
}
