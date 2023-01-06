<?php

namespace App\Http\Controllers;
use App\Models\holidaylist;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class HolidaylistController extends Controller
{
    public function getOptionalLeaves(){
        $optionalLeaveData = [];
        $today = Carbon::now()->format('Y-m-d');
        
        $getOptionalLeaveList = holidaylist::from('holidaylists as hl')
        ->where('hl.region', '=', 'IN')
        ->where('hl.holidaytype', '=', 'optional')
        ->where('hl.holidaydate', '>=', $today)
        ->orderBy('hl.holidaydate')
        ->get(['hl.id', 'hl.holidayname', 'hl.holidaytype', 'hl.holidaydate', 'hl.region']);

        array_push($optionalLeaveData, ["optionalLeaveData" => $getOptionalLeaveList]);

        return $optionalLeaveData;
          
    }
}
