<?php

use Illuminate\Database\Seeder;

class SeqSchemeGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seq_scheme_group')->insert([
            'group_name' => 'Pertamina',
            'group_desc' => 'Pertamina dan Gas',
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'from_date' => date('Y-m-d H:i:s'),
        ]);
    }
}
