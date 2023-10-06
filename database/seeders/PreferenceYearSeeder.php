<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PreferenceYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_years')->insert([
            [
                'label' => '2023',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2024',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2025',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2026',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2027',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2028',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2029',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => '2030',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
