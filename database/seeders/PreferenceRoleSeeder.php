<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PreferenceRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_roles')->insert([
            [
                'label' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Supervisor',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Encoder',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Citizen',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
