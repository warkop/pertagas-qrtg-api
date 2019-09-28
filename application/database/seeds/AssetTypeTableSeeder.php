<?php

use Illuminate\Database\Seeder;

class AssetTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('asset_type')->insert([
            'asset_name' => 'Bright Gas 220 Gram',
            'asset_desc' => 'Bright Gas 220 Gram',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('asset_type')->insert([
            'asset_name' => 'Bright Gas 5,5Kg',
            'asset_desc' => 'Bright Gas 5,5Kg',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('asset_type')->insert([
            'asset_name' => 'Bright Gas 12Kg',
            'asset_desc' => 'Bright Gas 12Kg',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('asset_type')->insert([
            'asset_name' => 'Ease Gas 9Kg',
            'asset_desc' => 'Ease Gas 9Kg',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('asset_type')->insert([
            'asset_name' => 'Ease Gas 14Kg',
            'asset_desc' => 'Ease Gas 14Kg',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('asset_type')->insert([
            'asset_name' => 'Elpiji Gas 3Kg',
            'asset_desc' => 'Elpiji Gas 3Kg',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('asset_type')->insert([
            'asset_name' => 'Elpiji Gas 12Kg',
            'asset_desc' => 'Elpiji Gas 12Kg',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
    }
}
