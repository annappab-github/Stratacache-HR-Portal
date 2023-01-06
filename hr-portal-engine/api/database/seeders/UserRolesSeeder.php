<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Excel;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ["1","Create-User","sanctum"],
            ["2","Edit-User","sanctum"],
            ["3","Delete-User","sanctum"],
            ["4","View-User","sanctum"],
            ["5","Create-Role","sanctum"],
            ["6","Edit-Role","sanctum"],
            ["7","Delete-Role","sanctum"],
            ["8","View-Role","sanctum"],
            ["9","Viewer","sanctum"],
            ["10","View-Audit","sanctum"],
            ["11","View-Employee-Detail","sanctum"],
            ["12","File-Access-Permission","sanctum"],
        ];

        foreach ($records as $record) {
            $permissions = $this->makePermission($record);
            $permissions->save();
        }

        $role = Role::create(['name' => 'Admin','guard_name' => 'sanctum']);
        $role->givePermissionTo('Create-User');
        $role->givePermissionTo('Edit-User');
        $role->givePermissionTo('Delete-User');
        $role->givePermissionTo('View-User');
        $role->givePermissionTo('Create-Role');
        $role->givePermissionTo('Edit-Role');
        $role->givePermissionTo('Delete-Role');
        $role->givePermissionTo('View-Role');
        $role->givePermissionTo('Viewer');
        $role->givePermissionTo('View-Audit');
        $role->givePermissionTo('View-Employee-Detail');
        $role->givePermissionTo('File-Access-Permission');


        $HRRole = Role::create(['name' => 'HR','guard_name' => 'sanctum']);
        $HRRole->givePermissionTo('Viewer');
        $HRRole->givePermissionTo('View-Employee-Detail');

        $employeeRole = Role::create(['name' => 'Employee','guard_name' => 'sanctum']);
        $employeeRole->givePermissionTo('Viewer');

        $user = \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@scala.com',
            'employee_id' => 'ADM01',
            'force_change_password' => true,
            'password' => Hash::make('Scala@123')
        ]);
        $user->assignRole($role);

        $hrmAudit = \App\Models\User::factory()->create([
            'name' => 'hrmaudit',
            'email' => 'hrmaudit@scala.com',
            'employee_id' => 'HRMAUDIT01',
            'force_change_password' => true,
            'password' => Hash::make('Scala@123')
        ]);
        $hrmAudit->assignRole($role);

        $excelPath = config('constants.employee_list_excel_path');
        $excelData = Excel::toArray([],$excelPath);
        foreach(array_slice($excelData[0],1) as $eachData) {
            if($eachData[1] != null) {
                $eachEmployee = \App\Models\User::factory()->create([
                    'name' => trim($eachData[4]),
                    'email' => trim($eachData[10]),
                    'employee_id' => trim($eachData[1]),
                    'force_change_password' => true,
                    'password' => Hash::make('Scala@123')
                ]);
                $eachEmployee->assignRole($employeeRole);
            }
        }
    }
    private function makePermission($record) {
        $permission = new Permission();
        $permission->id = $record[0];
        $permission->name = $record[1];
        $permission->guard_name = $record[2];
        return $permission;
    }
}
