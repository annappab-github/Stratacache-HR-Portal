<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee_status;

class EmployeeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = array(
            'Active',
            'Terminated',
            'Resigned');
        foreach($records as $status) {
            Employee_status::create([
                'status' => $status
            ]);
        }
    }
}
