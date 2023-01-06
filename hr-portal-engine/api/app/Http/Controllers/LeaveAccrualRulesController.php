<?php

namespace App\Http\Controllers;

use App\Models\LeaveAccrualRules;
use App\Models\Countries;
use App\Models\Leave_name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeaveAccrualRulesController extends Controller
{
    const GET_ALL_ENTITLEMENT_RULES = [self::class, 'getAllEntitlementRules'];
    const CREATE_ENTITLEMENT_RULES = [self::class, 'createRule'];
    const UPDATE_ENTITLEMENT_RULES = [self::class, 'UpdateRule'];

    public function getAllEntitlementRules() {
        $responseData = array();
        array_push($responseData, ["rules" => $this->getRules()]);
        array_push($responseData, ["allRegions" => $this->getRegions()]);
        array_push($responseData, ["leaveName" => $this->getLeaveNams()]);
        return $responseData;
    }

    public function getRules() {
        return LeaveAccrualRules::get();
    }

    public function getRegions() {
        return Countries::get(['id', 'country']);
    }

    public function getLeaveNams() {
        return Leave_name::get(['id', 'name']);
    }

    public function createRule(Request $request) {
        $LeaveAccrualRules = new LeaveAccrualRules;
        $LeaveAccrualRules->accrual = trim($request->accrual);
        $LeaveAccrualRules->carry_forword = $request->carry_forword;
        $LeaveAccrualRules->leave_type = $request->leave_type;
        $LeaveAccrualRules->region = $request->region;
        $LeaveAccrualRules->max_limit = $request->max_limit;
        $LeaveAccrualRules->save();

        $responseData = array();
        array_push($responseData, ["rules" => $this->getRules()]);
        array_push($responseData, ["allRegions" => $this->getRegions()]);
        array_push($responseData, ["leaveName" => $this->getLeaveNams()]);

        return response()->json([
            'response' => 'success',
            'updatedData' => $responseData
        ],200);
    }

    public function updateRule(Request $request) {
        $LeaveAccrualRules = LeaveAccrualRules::findOrFail($request->id);
        $LeaveAccrualRules->accrual = trim($request->accrual);
        $LeaveAccrualRules->carry_forword = $request->carry_forword;
        $LeaveAccrualRules->leave_type = $request->leave_type;
        $LeaveAccrualRules->region = $request->region;
        $LeaveAccrualRules->max_limit = $request->max_limit;
        $LeaveAccrualRules->save();

        $responseData = array();
        array_push($responseData, ["rules" => $this->getRules()]);
        array_push($responseData, ["allRegions" => $this->getRegions()]);
        array_push($responseData, ["leaveName" => $this->getLeaveNams()]);

        return response()->json([
            'response' => 'success',
            'updatedData' => $responseData
        ],200);
    }
}
