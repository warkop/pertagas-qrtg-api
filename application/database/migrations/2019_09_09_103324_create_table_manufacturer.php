<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableManufacturer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manufacturer', function (Blueprint $table) {
            $table->increments('manufacturer_id');
            $table->string('manufacturer_name')->nullable();
            $table->string('manufacturer_address')->nullable();
            $table->float('lat')->nullable();
            $table->float('long')->nullable();
            $table->datetime('created_by')->nullable();
            $table->datetime('updated_by')->nullable();
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
        Schema::dropIfExists('manufacturer');
    }
}
