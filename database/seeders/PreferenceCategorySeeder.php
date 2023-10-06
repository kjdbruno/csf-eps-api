<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PreferenceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_categories')->insert([
            [
                'label' => 'Agriculture and Fishery',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Ambulant Vendor',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Civil Regisrty Concern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'City Transaction',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Collection of Taxes and Fees',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Gender Issues',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Drainage',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Environmental Concern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Disaster-related Concern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Garbage Collection and Disposal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Health',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Human Resource',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Infrastructure Concern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Investment and Tourism',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Legislation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Legal Services',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Market Administration',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Business Permitting',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Zoning Clarances',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Parks Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Real Property Tax',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Public Safety',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Sanitation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Social Welfare',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Traffic and Parking Problem',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Public Transport Concern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Utility-related Concern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Veterinary Service',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Financial Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Covid-related Conern',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'label' => 'Youth-related Concern',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
