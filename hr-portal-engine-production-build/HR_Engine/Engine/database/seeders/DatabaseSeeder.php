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
      $this->call(county_citySeeder::class);
      $this->call(employeegradeSeeder::class);
      $this->call(employeeTypeSeeder::class);
      $this->call(deparmentsSeeder::class);
      $this->call(EmployeeStatusSeeder::class);
    }
}
