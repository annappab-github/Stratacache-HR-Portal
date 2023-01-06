<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserRolesSeeder;
use Database\Seeders\EmployeesSeeder;
use Database\Seeders\county_citySeeder;
use Database\Seeders\employeegradeSeeder;
use Database\Seeders\employeeTypeSeeder;
use Database\Seeders\deparmentsSeeder;
use Database\Seeders\EmployeeStatusSeeder;
use Database\Seeders\LeaveNameSeeder;
use Database\Seeders\EmployeeLeaveBalanceSeeder;
use Database\Seeders\ApplyleaveSeeder;
use Database\Seeders\HolidaylistSeeder;
use Database\Seeders\WorkingsaturdaysSeeder;
use Database\Seeders\LeaveEntitlementTableSeeder;
use Database\Seeders\LeaveAccrualRulesSeeder;
use Database\Seeders\EmployeeGender;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $this->call(UserRolesSeeder::class);
      $this->call(EmployeesSeeder::class);
      $this->call(EmployeeGender::class);
      $this->call(county_citySeeder::class);
      $this->call(employeegradeSeeder::class);
      $this->call(employeeTypeSeeder::class);
      $this->call(deparmentsSeeder::class);
      $this->call(EmployeeStatusSeeder::class);
      $this->call(LeaveNameSeeder::class);
      $this->call(EmployeeLeaveBalanceSeeder::class);
      $this->call(ApplyleaveSeeder::class);
      $this->call(HolidaylistSeeder::class);
      $this->call(WorkingsaturdaysSeeder::class);
      $this->call(LeaveEntitlementTableSeeder::class);
      $this->call(LeaveAccrualRulesSeeder::class);
    }
}
