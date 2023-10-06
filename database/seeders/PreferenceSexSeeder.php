<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PreferenceSexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_sexes')->insert([
            [
                'label' => 'Male',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Female',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
