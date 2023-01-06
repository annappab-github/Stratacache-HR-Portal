<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departments;

class deparmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = array(
            'Executive Management',
            'People, Performance & Culture',
            'Technology & Innovation',
            'Research & Development',
            'Business Operations',
            'Customer Development',
            'Marketing',
            'MD Office',
            'Projects',
            'Finance',
            'Legal',
            'Sales');
        foreach($departments as $depertment) {
            Departments::create([
                'deparment' => $depertment
            ]);
        }
    }
}
