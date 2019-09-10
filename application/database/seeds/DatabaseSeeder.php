<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UsersTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('AssetTypeTableSeeder');
        $this->call('ManufacturerTableSeeder');
        $this->call('ResultsTableSeeder');
        $this->call('SeqSchemeGroupTableSeeder');
        $this->call('SeqSchemeTableSeeder');
        $this->call('StationsTableSeeder');
    }
}
