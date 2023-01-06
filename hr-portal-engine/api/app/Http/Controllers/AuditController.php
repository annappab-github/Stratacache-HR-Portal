<?php

namespace App\Http\Controllers;
use App\Models\User;
use Spatie\Permission\Models\Role;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditController extends Controller
{
    public function getAll(){
        $all = Audit::all();
        $role = Role::all();
        $user = User::all();

        return response()->json([
            'audit' => $all,
            'role' => $role,
            'user' => $user,
        ], 200);
    }

    public function createAuditForFiles(Request $request) {
        $auditArray = $request->all();
        foreach($auditArray as $eachAudit){
            $newAudit = new Audit;
            $newAudit->user_type = $eachAudit['user_type'];
            $newAudit->auditable_type = $eachAudit['auditable_type'];
            $newAudit->auditable_id = $eachAudit['auditable_id'];
            $newAudit->event = $eachAudit['event'];
            $newAudit->created_at = $eachAudit['created_at'];
            $newAudit->updated_at = $eachAudit['updated_at'];
            $newAudit->save();
        }
    }
}
