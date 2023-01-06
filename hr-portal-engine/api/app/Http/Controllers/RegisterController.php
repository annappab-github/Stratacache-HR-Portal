<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request){
        // $request -> validate([
        //     'name' => ['required'],
        //     'email' => ['required','email','unique:users'],
        //     'password' => ['required','min:6','confirmed']

        // ]);
        $checkUser = User::where('name',$request->name)->get();
        $checkUserEmail = User::where('email',$request->email)->get();
        if ($checkUser->count()>0){
            $msg = 'User already exists';
            return response()->json([
                'msg' => $msg,
            ], 202);
        }
        else if ($checkUserEmail->count()>0){
            $msg = 'Email already used';
            return response()->json([
                'msg' => $msg,
            ], 203);
        }
        else{
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            return response()->json("User Registered", 200);
        }

    }
}
