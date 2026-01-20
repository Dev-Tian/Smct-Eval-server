<?php

namespace Database\Seeders;

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
            ['department_name' => 'Automation',                 'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Audit',                      'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Sales and Marketing',        'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Human Resources',            'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Compensation and Benefits',  'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Accounting',                 'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'Tax and Legal',              'created_at' => now(), 'updated_at' => now()],
            ['department_name' => 'IT',                         'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('departments')->insert($departments);
    }
}
