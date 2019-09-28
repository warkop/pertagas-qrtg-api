<?php

use Illuminate\Database\Seeder;

class SeqSchemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // starting tube
        DB::table('seq_scheme')->insert([
            'station_id' => 2,
            'predecessor_station_id' => 1,
            'result_id' => 13,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Unregistered New Cylinders',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
            
        // from mwh
        DB::table('seq_scheme')->insert([
            'station_id' => 3,
            'predecessor_station_id' => 2,
            'result_id' => 2,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Registered Newly Cylinders',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //start-----if Cylinders Type NV, R1, R2, R3, R4

        //NV
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 3,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type NV',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //R1
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 4,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R1',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //R2
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 5,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R2',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //R3
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 6,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R3',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //R4
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 7,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R4',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        //end-----if Cylinders Type NV, R1, R2, R3, R4

        // tube is valid
        DB::table('seq_scheme')->insert([
            'station_id' => 4,
            'predecessor_station_id' => 3,
            'result_id' => 1,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders Type V',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //yes, it can be filled
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 4,
            'result_id' => 9,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Filled-Cylinders Type F',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //no, that's R5
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 4,
            'result_id' => 8,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders R5',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // send to agents
        DB::table('seq_scheme')->insert([
            'station_id' => 7,
            'predecessor_station_id' => 5,
            'result_id' => 19,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Filled Delivery Orders',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        //start repair order

        // RO-R1
        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 14,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R1',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // RO-R2
        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 15,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R2',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // RO-R3
        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 16,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R3',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // RO-R4
        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 17,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R4',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // RO-R5
        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 18,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R5',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        //end repair order

        // unfixable
        DB::table('seq_scheme')->insert([
            'station_id' => 2,
            'predecessor_station_id' => 6,
            'result_id' => 11,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Rejected(Unfixable) Cylinders Type W',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // has been repaired
        DB::table('seq_scheme')->insert([
            'station_id' => 3,
            'predecessor_station_id' => 6,
            'result_id' => 10,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repaired Cylinders (Type X)',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // not valid tube
        DB::table('seq_scheme')->insert([
            'station_id' => 0,
            'predecessor_station_id' => 5,
            'result_id' => 3,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type NV will be inspected and double checked',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        // from agents
        DB::table('seq_scheme')->insert([
            'station_id' => 3,
            'predecessor_station_id' => 7,
            'result_id' => 12,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Empty Cylinders Cylinder Type E',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
    }
}
