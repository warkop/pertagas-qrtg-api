<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSeqScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seq_scheme', function (Blueprint $table) {
            $table->bigIncrements('seq_scheme_id');
            $table->integer('station_id')->nullable();
            $table->integer('predecessor_station_id')->nullable();
            $table->integer('result_id')->nullable();
            $table->integer('seq_scheme_group_id')->nullable();
            $table->string('scheme_name')->nullable();
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
        Schema::dropIfExists('seq_scheme');
    }
}
