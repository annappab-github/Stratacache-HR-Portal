<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use EmailController;
use App\Models\Employee_leave_balance;
use App\Models\Leave_name;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.index');
    }

    public function getAll(){
        // $users = User::latest()->get();

        $users = User::from("users as ur")
        ->join('employees as em', 'em.empid', '=', 'ur.employee_id')
        ->where('em.display_to_employees', 1)
        ->orderBy('em.location', 'desc')
        ->orderBy('em.empid')
        ->get();

        // $roles = $users->getRoleNames();
        $users->transform(function($user){
            $user->role = $user->getRoleNames()->first();
        //     $user->userPermissions = $user->getPermissionNames();
            return $user;
        });

        return response()->json([
            'users' => $users
        ], 200);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //     'name' => 'required|string',
        //     'password' => 'required|min:6',
        //     'role' => 'required',
        //     'email' => 'required|email|unique:users'
        // ]);
        $checkUserEmail = User::where('email',$request->email)->get();
        $checkEmployeeID = User::where('employee_id',$request->empid)->get();
        if ($checkEmployeeID->count()>0){
            $msg = 'Employee ID already used';
            return response()->json([
                'msg' => $msg,
            ], 202);
        } else if ($checkUserEmail->count()>0){
            $msg = 'Email already used';
            return response()->json([
                'msg' => $msg,
            ], 203);
        } else{
            $user = new User();
            $employees = new Employees();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->employee_id = $request->empid;
            $user->force_change_password = $request->force_change_password;
            $user->password = bcrypt($request->password);

            $user->assignRole($request->role);
            $user->save();

            $employees->empid = trim($request->empid);
            $employees->start_Date = $request->start_Date;
            $employees->employee_grade = $request->employee_grade;
            $employees->location = $request->location;
            $employees->city= $request->city;
            $employees->position = $request->position;
            $employees->department = $request->department;
            $employees->land_ext = $request->land_ext;
            $employees->mobile_number = $request->mobile_number;
            $employees->reporting_to = $request->reporting_to;
            $employees->birth_dates = $request->birth_dates;
            $employees->address = $request->address;
            $employees->employement_type = $request->employement_type;
            $employees->probation_period = $request->probation_period;
            $employees->confirmation_date = $request->confirmation_date;
            $employees->remarks = $request->remarks;
            $employees->new_confirmationdate = $request->new_confirmationdate;
            $employees->status = $request->status;
            $employees->terminationdate = $request->terminationdate;
            $employees->resignation = $request->resignation;
            $employees->last_workingdate = $request->last_workingdate;

            $employees->save();

            try {
                // $this->addLeaveBalance('IN0037');
                $leave_names = Leave_name::all();
                foreach($leave_names as $leave) {
                    $Employee_leave_balance = new Employee_leave_balance();
                    $Employee_leave_balance->empid = trim($request->empid);
                    $Employee_leave_balance->balance = '';
                    $Employee_leave_balance->leave_id = $leave['id'];
                    $Employee_leave_balance->save();
                }
            }
            catch(Exception $e) {
                Log::debug($e->getMessage());
            }

            try {
                $request->merge(["type" => "New Employee", "subject" => "Welcome Aboard", "from" => "notifications@theportal.scala-apac.org"]);
                app('App\Http\Controllers\EmailController')->sendEmail($request);
                
                $request->merge(["type" => "New Reportee", "subject" => "Welcome Aboard", "from" => "notifications@theportal.scala-apac.org"]);
                app('App\Http\Controllers\EmailController')->sendEmail($request);
            }
            catch(Exception $e) {
                Log::debug($e->getMessage());
            }


            
            return $employees;
        }
    }

    public function addLeaveBalance($empId) {
        Log::debug('====>', $empId);
        // $leave_names = Leave_name::all();
        // foreach($leave_names as $leave) {
        //     $addLEave = Employee_leave_balance::create([
        //         'empid' => 'IN3337',
        //         'balance' => '',
        //         'leave_id' => $leave['id']
        //     ]);
        // }
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
    public function update(Request $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|string',
        //     'password' => 'nullable|min:6',
        //     'roles' => 'required',
        //     'email' => 'required|email|'
        // ]);
        $checkEmployeeID = User::where('employee_id',trim($request->empid))->get();
        $checkUserEmail = User::where('email',$request->email)->get();

        if ($checkEmployeeID->count()>0 &&  trim($request->empid) != $request->employeeIdCheck){
            $msg = 'Employee ID already used';
            return response()->json([
                'msg' => $msg,
            ], 202);
        } else if ($checkUserEmail->count()>0 &&  $request->email!=$request->emailCheck){
            $msg = 'Email already used';
            return response()->json([
                'msg' => $msg,
            ], 203);
        } else{
            $user = User::findOrFail($request->urid);
            
            $user->name = $request->name;
            $user->email = $request->email;
            $user->employee_id = trim($request->empid);
            if($request->has('force_change_password')){
                if($request->force_change_password == 'true'){
                    $user->force_change_password = 1;
                } else{
                    $user->force_change_password = 0;
                }
            }
            
            
            if($request->has('password') && $request->password!=null){
                $user->password = bcrypt($request->password);
            }
            
            if($request->has('role')){
                $userRole = $user->getRoleNames();
                foreach($userRole as $role){
                    $user->removeRole($role);
                }
            }
            $user->assignRole($request->role);
            $user->save();

            $employees = Employees::findOrFail($request->emid);
            $old_reportingTo = $employees->reporting_to;

            // $employees->empid = $request->empid;
            $employees->start_Date = $request->start_Date;
            $employees->employee_grade = $request->employee_grade;
            $employees->location = $request->location;
            $employees->city = $request->city;
            $employees->position = $request->position;
            $employees->department = $request->department;
            $employees->land_ext = $request->land_ext;
            $employees->mobile_number = $request->mobile_number;
            $employees->reporting_to = $request->reporting_to;
            $employees->birth_dates = $request->birth_dates;
            $employees->address = $request->address;
            $employees->employement_type = $request->employement_type;
            $employees->probation_period = $request->probation_period;
            $employees->confirmation_date = $request->confirmation_date;
            $employees->remarks = $request->remarks;
            $employees->new_confirmationdate = $request->new_confirmationdate;
            $employees->status = $request->status;
            $employees->terminationdate = $request->terminationdate;
            $employees->resignation = $request->resignation;
            $employees->last_workingdate = $request->last_workingdate;
            $employees->gender = $request->gender;
            
            

            $employees->save();

            if($old_reportingTo != $request->reporting_to){
                $request->merge(["type" => "New Reportee", "subject" => "Welcome Aboard", "from" => "no-reply@stratacache.com"]);
                app('App\Http\Controllers\EmailController')->sendEmail($request);
            }

            return response()->json([
                'test' => $request->password,
            ],200);

        }

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



    /////////////////////// User defined Method


    public function profile(){
        return view("profile.index");
    }

    public function postProfile(Request $request){
        $user = auth()->user();
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id
        ]);

        $user->update($request->all());

        return redirect()->back()->with('success', 'Profile Successfully Updated');
    }

    public function getPassword(){
        return view('profile.password');
    }

    public function postPassword(Request $request){

        $this->validate($request, [
            'newpassword' => 'required|min:6|max:30|confirmed'
        ]);

        $user = auth()->user();

        $user->update([
            'password' => bcrypt($request->newpassword)
        ]);

        return redirect()->back()->with('success', 'Password has been Changed Successfully');
    }

    public function delete($id){
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json('ok', 200);
    }

    public function search(Request $request){
        $searchWord = $request->get('s');
        $users = User::where(function($query) use ($searchWord){
            $query->where('name', 'LIKE', "%$searchWord%")
            ->orWhere('email', 'LIKE', "%$searchWord%");
        })->latest()->get();

        $users->transform(function($user){
            $user->role = $user->getRoleNames()->first();
            $user->userPermissions = $user->getPermissionNames();
            return $user;
        });

        return response()->json([
            'users' => $users
        ], 200);

    }

    public function getOne($id){
        $existingUser = User::find($id);
        // $roles = $existingUser->getRoleNames(); //this is to get role name of single user
        $pwd = $existingUser->getAuthPassword();
        // $data = $existingUser + $pwd;
        return $existingUser;
    }
    public function getCurrent(){
        $id = User::find(Auth::id());
        return $id;
    }
    public function getUser($email){
        $existingUser = User::where('email', $email);
        return $existingUser;
    }

    public function changePassword(Request $request){
        $user = User::findOrFail(Auth::id());
        $this->validate($request, [
            'newpassword' => 'required',
        ]);
        if (!Hash::check($request->newpassword, $user->password)) {
            if($request->has('newpassword')){
                $user->password = bcrypt($request->newpassword);
            }
            $user->save();

            return response()->json([
                'getUser' => $user,
                'password' => $request->newpassword,
                'hashpassword' => Hash::check($request->newpassword, $user->password)
            ], 200);
        } else{
            $msg = 'Old Password is incorrect';
            return response()->json([
                'msg' => $msg,
            ], 202);
        }
    }

    public function forceChangePassword(Request $request){
        $user = User::findOrFail(Auth::id());
        $this->validate($request, [
            // 'password' => 'required',
            'new_password' => 'different:password',
        ]);
        if (Hash::check($request->password, $user->password)) { 
            if($request->has('new_password')){
                $user->password = bcrypt($request->new_password);
                $user->force_change_password = true;
            }
            $user->save();

            return response()->json([
                'getUser' => $user,
                'password' => $request->password,
                'hashpassword' => Hash::check($request->password, $user->password)
            ], 200);
        } else{
            $msg = 'Old Password is incorrect';
            return response()->json([
                'msg' => $msg,
            ], 202);
        }
    }

    public function forgotPasswordReset(Request $request){
        $user = User::where('email', $request->email)->first();
        $this->validate($request, [
            'new_password' => 'required',
            // 'new_password' => 'different:password',
        ]);
        if($request->has('new_password')){
            $user->password = bcrypt($request->new_password);
            // $user->force_change_password = true;
        }
        $user->save();

        return response()->json([
            'getUser' => $user,
            'password' => $request->password,
        ], 200);

    }
    
}
