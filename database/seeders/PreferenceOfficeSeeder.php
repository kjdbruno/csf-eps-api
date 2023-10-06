<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PreferenceOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_offices')->insert([
            [
                'label' => 'City Information & Communication Technology Office',
                'code' => 'CICTO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Mayor',
                'code' => 'OCM',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Vice Mayor',
                'code' => 'OCVM',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Administrator',
                'code' => 'ADM',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Accountant',
                'code' => 'ACA',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Agriculture',
                'code' => 'AGR',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Budget Officer',
                'code' => 'CBO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Health Officer',
                'code' => 'CHO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Legal Officer',
                'code' => 'CLO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Treasurer',
                'code' => 'CTO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Engineering & Architectural Services',
                'code' => 'EAS',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Environment & Natural Resources',
                'code' => 'CENRO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City General Services Officer',
                'code' => 'GSO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the Corporate Relation Officer',
                'code' => 'CRO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Market Supervisor',
                'code' => 'EEM',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Assessor',
                'code' => 'OCA',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office for Public Safety',
                'code' => 'OPS',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office for Strategic Management',
                'code' => 'OSM',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of of Secretary to the Sangguanian Panglungsod',
                'code' => 'OSSP',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Planning & Development Coordinator',
                'code' => 'PDO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Local Economic Business Development Office',
                'code' => 'LEBDO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Registrar',
                'code' => 'REG',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Social Welfare & Development Officers',
                'code' => 'CSWD',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Veterenarian',
                'code' => 'VET',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Human Resource & Management Officer',
                'code' => 'HRMO',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
            [
                'label' => 'Office of the City Librarian',
                'code' => 'LIB',
                'created_at' => NOW(),
                'updated_at' =>NOW()
            ],
        ]);
    }
}
