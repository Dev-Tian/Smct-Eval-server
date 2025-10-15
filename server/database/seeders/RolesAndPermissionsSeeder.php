<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

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
        $role_employee = Role::create(['name' => 'employee']);
        $role_employee->givePermissionTo('view_own_profile');
        $role_employee->givePermissionTo('view_own_evaluations');
        $role_employee->givePermissionTo('submit_self_assessment');

        $role_manager = Role::create(['name' => 'manager']);
        $role_manager->givePermissionTo('view_team_profiles');
        $role_manager->givePermissionTo('conduct_evaluations');
        $role_manager->givePermissionTo('view_team_reports');
        $role_manager->givePermissionTo('approve_goals');

        $role_hr = Role::create(['name' => 'hr']);
        $role_hr->givePermissionTo('view_all_profiles');
        $role_hr->givePermissionTo('manage_evaluations');
        $role_hr->givePermissionTo('generate_reports');
        $role_hr->givePermissionTo('manage_users');
        $role_hr->givePermissionTo('view_hr_reports');

        $role_hr_manager = Role::create(['name' => 'hr manager']);
        $role_hr_manager->givePermissionTo('view_all_profiles');
        $role_hr_manager->givePermissionTo('manage_evaluations');
        $role_hr_manager->givePermissionTo('generate_reports');
        $role_hr_manager->givePermissionTo('manage_users');
        $role_hr_manager->givePermissionTo('view_hr_reports');
        $role_hr_manager->givePermissionTo('approve_hr_actions');
        $role_hr_manager->givePermissionTo('manage_hr_policies');

        $role_evaluator = Role::create(['name' => 'evaluator']);
        $role_evaluator->givePermissionTo('conduct_evaluations');
        $role_evaluator->givePermissionTo('view_evaluation_reports');
        $role_evaluator->givePermissionTo('manage_evaluation_templates');

        $role_admin = Role::create(['name' => 'admin']);
        $role_admin->givePermissionTo(Permission::all());
        $role_admin->givePermissionTo('system_administration');
        $role_admin->givePermissionTo('user_management');

    }
}

