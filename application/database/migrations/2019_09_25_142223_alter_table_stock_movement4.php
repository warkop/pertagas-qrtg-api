<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockMovement4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_movement', function (Blueprint $table) {
            $table->dropColumn('report_type_id');
            $table->dropColumn('station_id');
            $table->dropColumn('destination_station_id');
            $table->dropColumn('document_number');
            $table->dropColumn('ref_doc_number');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_movement', function (Blueprint $table) {
            //
        });
    }
}
