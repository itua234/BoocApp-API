<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')
        ->insert([
            [
                "name" => 'user',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "name" => 'chef',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "name" => 'admin',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "name" => 'super admin',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
        ]);
    }
}
