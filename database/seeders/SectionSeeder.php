<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['section'  => 'Section1',      'created_at'  => now(),     'updated_at'    =>  now()],
            ['section'  => 'Section2',      'created_at'  => now(),     'updated_at'    =>  now()],
            ['section'  => 'Section3',      'created_at'  => now(),     'updated_at'    =>  now()],
            ['section'  => 'Section4',      'created_at'  => now(),     'updated_at'    =>  now()],
        ];
        DB::table('sub_sections')->insert($sections);
    }
}
