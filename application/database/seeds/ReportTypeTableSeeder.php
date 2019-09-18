<?php

use Illuminate\Database\Seeder;

class ReportTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seq_scheme')->insert([
            'report_name' => 'Good Issue',
            'report_desc' => null,
            'can_be_ref' => null,
            'has_designation' => null,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);

        DB::table('seq_scheme')->insert([
            'report_name' => 'Good Receive',
            'report_desc' => null,
            'can_be_ref' => null,
            'has_designation' => null,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
    }
}
