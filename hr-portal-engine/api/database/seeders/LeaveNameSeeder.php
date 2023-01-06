<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Leave_name;
use Illuminate\Support\Facades\Log;
use Excel;
class LeaveNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = array(
            'Annual Leave Balance',
            'Sick Leave Balance',
            'Optional Leave Balance ',
            'Compensatory Leave Balance',
            'Marriage Leave Balance',
            'Bereavement Leave Balance',
            'Maternity Leave Balance',
            'Paternity Leave Balance',
            'Childcare Leave Balance',
            'Carers Leave Balance',
            'National Service Leave Balance',
            'Hospital Leave Balance',
            'Unpaid Leave Balance');
            
        foreach($records as $record) {
            Leave_name::create([
                'name' => trim($record)
            ]);
        }
    }
}
