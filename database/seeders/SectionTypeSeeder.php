<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types =
            [
                ['section_type'     =>  'type1'],
                ['section_type'     =>  'type2'],
                ['section_type'     =>  'type3'],
                ['section_type'     =>  'type4'],
            ];

        DB::table('section_types')->insert($types);
    }
}
