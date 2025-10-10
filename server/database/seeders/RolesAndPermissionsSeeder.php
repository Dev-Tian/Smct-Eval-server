<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        //employee role
        Permission::create(['name' => 'view_own_profile']);
        Permission::create(['name' => 'view_own_evaluations']);
        Permission::create(['name' => 'submit_self_assessment']);

        //manager role
        Permission::create(['name' => 'view_team_profiles']);
        Permission::create(['name' => 'conduct_evaluations']); //evaluator too
        Permission::create(['name' => 'view_team_reports']);
        Permission::create(['name' => 'approve_goals']);

        //hr and hr-manager role
        Permission::create(['name' => 'view_all_profiles']);
        Permission::create(['name' => 'manage_evaluations']);
        Permission::create(['name' => 'generate_reports']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'view_hr_reports']);

        Permission::create(['name' => 'approve_hr_actions']);
        Permission::create(['name' => 'manage_hr_policies']);

        //evaluator role
        Permission::create(['name' => 'view_evaluation_reports']);
        Permission::create(['name' => 'manage_evaluation_templates']);

        //admin role
        Permission::create(['name' => 'system_administration']);
        Permission::create(['name' => 'user_management']);



        // Create roles and assign created permissions
        $role = Role::create(['name' => 'employee']);
        $role->givePermissionTo('view_own_profile');
        $role->givePermissionTo('view_own_evaluations');
        $role->givePermissionTo('submit_self_assessment');

        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo('view_team_profiles');
        $role->givePermissionTo('conduct_evaluations');
        $role->givePermissionTo('view_team_reports');
        $role->givePermissionTo('approve_goals');

        $role = Role::create(['name' => 'hr']);
        $role->givePermissionTo('view_all_profiles');
        $role->givePermissionTo('manage_evaluations');
        $role->givePermissionTo('generate_reports');
        $role->givePermissionTo('manage_users');
        $role->givePermissionTo('view_hr_reports');

        $role = Role::create(['name' => 'hr manager']);
        $role->givePermissionTo('view_all_profiles');
        $role->givePermissionTo('manage_evaluations');
        $role->givePermissionTo('generate_reports');
        $role->givePermissionTo('manage_users');
        $role->givePermissionTo('view_hr_reports');
        $role->givePermissionTo('approve_hr_actions');
        $role->givePermissionTo('manage_hr_policies');

        $role = Role::create(['name' => 'evaluator']);
        $role->givePermissionTo('conduct_evaluations');
        $role->givePermissionTo('view_evaluation_reports');
        $role->givePermissionTo('manage_evaluation_templates');

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
        $role->givePermissionTo('system_administration');
        $role->givePermissionTo('user_management');
    }
}

