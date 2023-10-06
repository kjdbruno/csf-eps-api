<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_admins')->insert([
            'userID' => 1,
            'officeID' => 1,
            'positionID' => 1,
            'yearID' => 1,
            'employeeID' => 2017043,
            'isVerified' => TRUE,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
