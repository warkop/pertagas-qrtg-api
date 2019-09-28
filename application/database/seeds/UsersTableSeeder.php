<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'energeekmail@gmail.com',
            'username' => 'super_admin',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' =>  'sppbe1' . '@gmail.com',
            'username' => 'sppbe1',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 2,
            'station_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'sppbe2' . '@gmail.com',
            'username' => 'sppbe2',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 2,
            'station_id' => 4,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'sppbe3' . '@gmail.com',
            'username' => 'sppbe3',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 2,
            'station_id' => 5,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'bpt1' . '@gmail.com',
            'username' => 'bpt1',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 3,
            'station_id' => 6,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'bpt2' . '@gmail.com',
            'username' => 'bpt2',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 3,
            'station_id' => 6,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'mwh1' . '@gmail.com',
            'username' => 'mwh1',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 4,
            'station_id' => 2,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'mwh2' . '@gmail.com',
            'username' => 'mwh2',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 4,
            'station_id' => 2,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'agents1' . '@gmail.com',
            'username' => 'agents1',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 5,
            'station_id' => 7,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'agents2' . '@gmail.com',
            'username' => 'agents2',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 5,
            'station_id' => 7,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'manufacturer1' . '@gmail.com',
            'username' => 'manufacturer1',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 6,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
        DB::table('users')->insert([
            'email' => 'manufacturer2' . '@gmail.com',
            'username' => 'manufacturer2',
            'password' => app('hash')->make('3n3rg33k'),
            'role_id' => 6,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
            'from_date' => date('Y-m-d', strtotime('2019-01-01')),
            'end_date' => date('Y-m-d', strtotime('2040-12-31')),
        ]);
    }
}
