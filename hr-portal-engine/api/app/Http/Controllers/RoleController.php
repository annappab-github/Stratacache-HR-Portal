<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }
    public function index()
    {
        $roles= $this->role::all();
        return view('role.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|string|unique:roles',
        //     'permissions' => 'required'
        // ]);
        $checkRole = Role::where('name',$request->name)->get();
        if ($checkRole->count()>0){
            $msg = 'Role already exists';
            return response()->json([
                'msg' => $msg,
                'test' => $request->permissions
            ], 202);
        }
        else{
            $role = $this->role->create([
                'name' => $request->name,
            ]);
            if($request->has("permissions")){
                $role->givePermissionTo($request->permissions);
            }
            return response()->json("Role Created", 200);
        }




    }

    public function getAll(){
        $roles = $this->role->all();
        return response()->json([
            'roles' => $roles
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //     'name' => 'required|string',
        //     'permissions' => 'required'
        // ]);
        $checkRole = Role::where('name',$request->name)->get();
        // return $checkRole->pluck('name');
        if ($checkRole->count()>0 && $request->name!=$request->nameCheck){
            $msg = 'Role already exists';
            return response()->json([
                'msg' => $msg,
                'test' => $request
            ], 202);
        }
        else{
            $role = Role::findOrFail($id);
            $role->revokePermissionTo(Permission::all());
            $role->update([
                'name' => $request->name,
            ]);
            if($request->has("permissions")){
                $role->givePermissionTo($request->permissions);
            }
            return response()->json("Role Updated", 200);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $users = User::role($role)->get();

        if($users->count()>0){
            $msg = 'Role is in use, unassign the role to delete';
            return response()->json([
                'msg' => $msg
            ], 202);
        }
        else{
            $msg = 'Role Deleted Successfully';
            $role->delete();
            return response()->json([
                'msg' => $msg
            ], 200);
        }
    }
    public function getOne($id){
        $existingRole = Role::find($id);
        return $existingRole;
    }
}
