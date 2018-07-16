<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPivotPackageMarkers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_markers', function (Blueprint $table) {
            $table->dropIndex(['package_detail_id', 'type']);
            $table->index('package_detail_id');
            $table->dropColumn('type'); //category, tag, label, inclusions, exclusions, activity, theme
            $table->dropColumn('title');
            $table->dropColumn('content');
            $table->dropColumn('content_id');
            $table->dropColumn('user_id');
            $table->unsignedInteger('data_marker_id')->after('package_detail_id')->default(0);
            $table->unsignedInteger('package_id')->after('id')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_markers', function (Blueprint $table) {
            $table->dropIndex(['package_detail_id']);
            $table->dropColumn('data_marker_id');
            $table->dropColumn('package_id');
            $table->string('type', 10); //category, tag, label, inclusions, exclusions, activity, theme
            $table->string('title', 50);
            $table->string('content', 500)->nullable()->default(null);
            $table->unsignedInteger('content_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->index(['package_detail_id', 'type']);
        });
    }
}
