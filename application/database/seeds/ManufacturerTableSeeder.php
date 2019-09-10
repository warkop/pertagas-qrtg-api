<?php

use Illuminate\Database\Seeder;

class ManufacturerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('manufacturer')->insert([
            'manufacturer_name' => 'SPPBE',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
