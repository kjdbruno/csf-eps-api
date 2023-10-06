<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PreferencePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_positions')->insert([
            'label' => 'Administrative Assistant I',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
