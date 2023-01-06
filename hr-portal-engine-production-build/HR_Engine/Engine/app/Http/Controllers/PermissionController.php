<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
        $this->middleware(['auth']);

        // $this->permission = $permission;
        // $this->middleware(['auth', 'role_or_permission:admin|create role|create permission']);

    }

    public function index()
    {
        $permissions = $this->permission::all();

        return $permissions;

    }

    public function getAllPermissions(){
        $permissions = $this->permission::all();

        return response()->json([
            'permissions' => $permissions
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("permission.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required'
        ]);

        $permission = $this->permission->create([
            'name' => $request->name
        ]);

        return $permission;
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
    public function getAll(){
        $permissions = $this->permission->all();
        return $permissions;
    }

    public function getRolePermission($id){
        $permission = Permission::role($id)->get();
        return $permission;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
