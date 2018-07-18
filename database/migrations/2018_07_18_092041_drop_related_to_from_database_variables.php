<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRelatedToFromDatabaseVariables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('database_variables', function (Blueprint $table) {
            $table->dropColumn('related');
            $table->dropColumn('property');
        });
        Schema::table('database_variables', function (Blueprint $table) {
            $table->string('property', 50)->nullable()->after('object');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('database_variables', function (Blueprint $table) {
            $table->boolean('related')->default(false);
        });
    }
}
