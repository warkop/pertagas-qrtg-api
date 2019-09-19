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
        DB::table('seq_scheme')->insert([
            'station_id' => 2,
            'predecessor_station_id' => null,
            'result_id' => 13,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Unregistered New Cylinders',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 2,
            'predecessor_station_id' => null,
            'result_id' => null,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Unregistered New Cylinders',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
        
        DB::table('seq_scheme')->insert([
            'station_id' => 3,
            'predecessor_station_id' => 2,
            'result_id' => 2,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Registered Newly Cylinders',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        //start-----if Cylinders Type NV, R1, R2, R3, R4
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 3,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type NV',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 4,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R1',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 5,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R2',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 6,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R3',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 3,
            'result_id' => 7,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type R4',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
        //end-----if Cylinders Type NV, R1, R2, R3, R4

        //yes
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 4,
            'result_id' => 9,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Filled-Cylinders Type F',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        //no
        DB::table('seq_scheme')->insert([
            'station_id' => 5,
            'predecessor_station_id' => 4,
            'result_id' => 8,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders R5',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
        //start repair order
        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 8,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R5',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 4,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R1',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 5,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R2',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 6,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R3',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 7,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Cylinders R4',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
        //end repair order

        DB::table('seq_scheme')->insert([
            'station_id' => 2,
            'predecessor_station_id' => 6,
            'result_id' => 11,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repair Order Rejected(Unfixable) Cylinders Type W',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 3,
            'predecessor_station_id' => 6,
            'result_id' => 10,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Repaired Cylinders (Type X)',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => null,
            'predecessor_station_id' => 5,
            'result_id' => 3,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders type NV will be inspected and double checked',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 6,
            'predecessor_station_id' => 5,
            'result_id' => 12,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Empty Cylinders Cylinder Type E',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'station_id' => 4,
            'predecessor_station_id' => 3,
            'result_id' => 1,
            'seq_scheme_group_id' => 1,
            'scheme_name' => 'Cylinders Type V',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
    }
}
