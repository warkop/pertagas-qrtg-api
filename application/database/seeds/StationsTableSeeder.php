<?php

use Illuminate\Database\Seeder;

class StationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stations')->insert([
            'station_name' => 'Manufacturer',
            'abbreviation' => 'MF',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('stations')->insert([
            'station_name' => 'Material Warehouse',
            'abbreviation' => 'MWH',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('stations')->insert([
            'station_name' => 'Pre-Filling Checking Plant',
            'abbreviation' => 'PFCP',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('stations')->insert([
            'station_name' => 'Filling Plant',
            'abbreviation' => 'FP',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('stations')->insert([
            'station_name' => 'Shipping Plant',
            'abbreviation' => 'SP',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('stations')->insert([
            'station_name' => 'Repair Plant',
            'abbreviation' => 'RP',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('stations')->insert([
            'station_name' => 'Agents',
            'abbreviation' => 'AG',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
    }
}
