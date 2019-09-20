<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->integer('role_id')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->nullable();
            $table->float('lat')->nullable();
            $table->float('long')->nullable();
            $table->date('from_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('token')->nullable();
            $table->string('device')->nullable();
            $table->string('os_type')->nullable();
            $table->string('imei')->nullable();
            $table->string('build_number')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('user_gcid')->nullable();
            $table->datetime('last_login')->nullable();
            $table->string('created_by')->nullable();
            $table->string('update_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
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
