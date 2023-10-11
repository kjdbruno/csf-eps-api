<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_months')->insert([
            [
                'label' => 'Jan',
                'code' => '01',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Feb',
                'code' => '02',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Mar',
                'code' => '03',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Apr',
                'code' => '04',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'May',
                'code' => '05',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Jun',
                'code' => '06',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Jul',
                'code' => '07',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Aug',
                'code' => '08',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Sep',
                'code' => '09',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Oct',
                'code' => '10',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Nov',
                'code' => '11',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Dec',
                'code' => '12',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
