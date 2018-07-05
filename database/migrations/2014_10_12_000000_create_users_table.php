<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->default(false);
            $table->string('email', 60)->unique();
            $table->string('password', 100);
            $table->string('api_token', 100)->default(null)->nullable();
            $table->rememberToken();
            $table->string('position', 50)->default("Data Manager");
            $table->boolean('admin')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->string('name', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
