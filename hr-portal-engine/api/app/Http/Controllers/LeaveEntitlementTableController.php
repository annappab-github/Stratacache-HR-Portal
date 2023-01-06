<?php

namespace App\Http\Controllers;

use App\Models\LeaveEntitlementTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeaveEntitlementTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const GET_EMP_ENTITLEMENT_DATA = [self::class, 'getEmpEntitlementData'];
    const CREATE_ENTITLEMENT_DATA = [self::class, 'createEntitlement'];
    const UPDATE_ENTITLEMENT_DATA = [self::class, 'UpdateEntitlement'];

    public function getEmpEntitlementData($empid)
    {
        $user = LeaveEntitlementTable::where('empid', $empid)->get();

        return $user;
    }

    public function createEntitlement(Request $request) {
        $LeaveEntitle = new LeaveEntitlementTable;
        foreach($request[0] as $key=>$value) {
            if(trim($value) != '' && $value != null) {
                $LeaveEntitle[$key] = trim($value);
            }
        }
        $LeaveEntitle->save();

        return response()->json([
            'response' => 'success',
        ],200);
    }

    public function UpdateEntitlement(Request $request) {
        $LeaveEntitle = LeaveEntitlementTable::findOrFail($request[0]['id']);
        foreach($request[0] as $key=>$value) {
            if(trim($value) != '' && $value != null) {
                $LeaveEntitle[$key] = trim($value);
            }
        }
        $LeaveEntitle->save();

        return response()->json([
            'response' => 'success',
        ],200);
    }
}
