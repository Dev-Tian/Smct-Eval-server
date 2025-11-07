<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department_name' => 'Engineering',        'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Product',            'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Design',             'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Marketing',          'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Human Resources',    'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Finance',            'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Sales',              'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Customer Support',    'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Operations',         'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Legal',              'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'IT',                 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('departments')->insert($departments);
    }
}
