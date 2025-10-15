<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuarterUsersEvaluaionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quarter =[
            ['title'=>'Quarter 1'],
            ['title'=>'Quarter 2'],
            ['title'=>'Quarter 3'],
            ['title'=>'Quarter 4'],
        ];
            DB::table('quarter_users_evaluaions')->insert($quarter);
    }
}
