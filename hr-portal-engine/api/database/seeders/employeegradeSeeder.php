<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee_grade;

class employeegradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $grades = array(
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4'
            );
        foreach($grades as $grade) {
            Employee_grade::create([
                'grade' => $grade
            ]);
        }
    }
}
