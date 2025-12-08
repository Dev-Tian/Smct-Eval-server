<?php

namespace Database\Seeders;

use App\Models\UsersEvaluation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersEvaluationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UsersEvaluation::factory()->count(20)->create();
    }
}
