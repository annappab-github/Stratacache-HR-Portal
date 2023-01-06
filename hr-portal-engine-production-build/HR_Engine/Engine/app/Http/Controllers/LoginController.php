<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Session;
use Illuminate\Support\Facades\Log;
use App\Models\Employees;

class LoginController extends Controller
{
    public function login(Request $request){
        $request -> validate([
            'email' => ['required'],
            'password' => ['required']
        ]);
        if(Auth::attempt($request->only('email','password'))){
            $user = Auth::User();

            $employee = Employees::where('empid', '=', $user->employee_id)
            ->whereIn('status', array('active', 'consultant'))
            ->get();
            if ($employee->count()>0){
                /*return response()->json($user,200);*/
                /*return response()->json([
                    'user'          => $user,
                    'session_token' => $user->createToken($request->device_name)->plainTextToken,
                ], 200);*/
                // $roles = $user->getRoleNames();
                //create token
                $permissions = $user->getPermissionsViaRoles();
                $roles = $user->getRoleNames();
                $token = $user->createToken('myapptoken')->plainTextToken;
                $response = [
                    'status'=>true,
                    'message'=>'Login successful!',
                    'data' =>[
                        'user'=>$user,
                        'token'=>$token,
                        'permissions'=>$permissions,
                        'roles'=>$roles
                    ]
                ];
                return response()->json($response,201);
            } else {
                return response()->json('Unauthorized Access',202);
            }
        }
        throw ValidationException::withMessages([
            'email'=>['The provided credentials are incorrect.']
        ]);
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return response(['message'=>'Logged Out Successfully']);
        // $request->user()->tokens()->delete();
        // $response = [
        //     'status'=>true,
        //     'message'=>'Logout successfully',
        // ];
        // return response($response,201);
    }

    /**
    * The user has been authenticated.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  mixed  $user
    * @return mixed
    */
    protected function authenticated(Request $request, $user)
    {
        $this->setUserSession($user);
    }
}
