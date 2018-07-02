<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_detail_id');
            $table->string('title', 100);
            $table->char('currency', 3);
            $table->unsignedDecimal('price_start', 8, 2);
            $table->unsignedDecimal('price_end', 8, 2)->nullable()->default(null);
            $table->unsignedInteger('discount_percent')->nullable()->default(null);
            $table->unsignedInteger('person')->default(1);
            $table->unsignedInteger('user_id')->default(0);
            $table->timestamps();
            $table->index('package_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_prices');
    }
}
