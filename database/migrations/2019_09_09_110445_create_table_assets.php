<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('asset_id');
            $table->integer('asset_type_id')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->integer('seq_schema_group_id')->nullable();
            $table->text('asset_desc')->nullable();
            $table->float('gross_weight')->nullable();
            $table->float('net_weight')->nullable();
            $table->string('pics_url')->nullable();
            $table->string('serial_number')->nullable();
            $table->datetime('manufacture_date')->nullable();
            $table->datetime('expiry_date')->nullable();
            $table->float('height')->nullable();
            $table->float('width')->nullable();
            $table->datetime('from_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('assets');
    }
}
