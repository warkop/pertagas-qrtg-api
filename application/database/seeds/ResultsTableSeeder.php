<?php

use Illuminate\Database\Seeder;

class ResultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('results')->insert([
            'result_name' => 'V',
            'result_desc' => 'Valid',
            'created_by' => 1,
            'station_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        
        DB::table('results')->insert([
            'result_name' => 'NR',
            'result_desc' => 'Newly Registered',
            'created_by' => 1,
            'station_id' => 2,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'NV',
            'result_desc' => 'Not Valid',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        
        
        DB::table('results')->insert([
            'result_name' => 'R1',
            'result_desc' => 'Retest',
            'created_by' => 1,
            'station_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R2',
            'result_desc' => 'Repaint',
            'created_by' => 1,
            'station_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R3',
            'result_desc' => 'Pasang Balancer & Repaint',
            'created_by' => 1,
            'station_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R4',
            'result_desc' => 'Pasang Balancer, Retest, Repaint',
            'created_by' => 1,
            'station_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R5',
            'result_desc' => 'Annealing',
            'created_by' => 1,
            'station_id' => 4,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'F',
            'result_desc' => 'Filled',
            'created_by' => 1,
            'station_id' => 4,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'X',
            'result_desc' => 'Repaired',
            'created_by' => 1,
            'station_id' => 6,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        
        DB::table('results')->insert([
            'result_name' => 'W',
            'result_desc' => 'Waste',
            'created_by' => 1,
            'station_id' => 6,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        
        DB::table('results')->insert([
            'result_name' => 'E',
            'result_desc' => 'Empty',
            'created_by' => 1,
            'station_id' => 7,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        
        DB::table('results')->insert([
            'result_name' => 'UR',
            'result_desc' => 'Unregistered',
            'created_by' => 1,
            'station_id' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R1',
            'result_desc' => 'Retest (Repair Order)',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R2',
            'result_desc' => 'Repaint (Repair Order)',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        
        DB::table('results')->insert([
            'result_name' => 'R3',
            'result_desc' => 'Pasang Balancer & Repaint (Repair Order)',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R4',
            'result_desc' => 'Pasang Balancer, Retest, Repaint (Repair Order)',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'R5',
            'result_desc' => 'Annealing (Repair Order)',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);

        DB::table('results')->insert([
            'result_name' => 'F',
            'result_desc' => 'Filled (deliver)',
            'created_by' => 1,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
    }
}
