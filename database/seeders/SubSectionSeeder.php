<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\SubSection;
use Illuminate\Database\Seeder;

class SubSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      SubSection::factory()->count(5)->create();
    }
}
