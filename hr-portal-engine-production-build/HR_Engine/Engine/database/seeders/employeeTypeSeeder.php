<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employment_type;

class employeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = array(
            'Full Time Employee',
            'Full Time Consultant',
            'Part Time Consultant',
            );
        foreach($types as $type) {
            Employment_type::create([
                'type' => $type
            ]);
        }
    }
}
