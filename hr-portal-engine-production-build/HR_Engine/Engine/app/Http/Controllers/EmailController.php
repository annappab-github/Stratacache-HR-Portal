<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mail;
use File;
use Storage;
use App\Models\Employees;

class EmailController extends Controller
{

    const SEND_EMAIL = [self::class, 'sendEmail'];


    public function sendEmail(Request $request) {
        $subject = 'The Portal['.$request->type.'] : '.$request->subject;

        $data = array(
            'url' => $request->message
        );

        if($request->type == "Reset Password Notification") {
            Mail::send('forgotpasswordemail', $data, function ($message) use ($request, $subject)
            {
                $message->to($request->to)->subject($subject);
                $message->from($request->from);
            });
        } else if($request->type == "New Employee") {
            $subject = 'THE PORTAL: Welcome to THE PORTAL, you are now connected.';
            $data = array(
                'url' => 'https://theportal.scala-apac.org/',
                'name' => $request->name,
                'username' => $request->email,
                'password' => $request->password
            );
            Mail::send('newemployeewelcome', $data, function ($message) use ($request, $subject)
            {
                $message->to($request->email)->subject($subject);
                $message->from($request->from);
            });
        } else if($request->type == "New Reportee") {
            if($request->reporting_to != ''){
                $managerData = Employees::from('employees as em')
                ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
                ->where('empid', '=', $request->reporting_to)
                ->get(['em.id', 'em.empid', 'ur.name', 'ur.email']);

                if(count($managerData) > 0) {
                    $subject = 'THE PORTAL: Hi Line Manager, You have a new team member.';
                    $data = array(
                        'manager_name' => $managerData[0]['name']
                    );

                    $request->merge(["manager_email" => $managerData[0]['email']]);

                    Mail::send('newemployeemanagernotifier', $data, function ($message) use ($request, $subject)
                    {
                        $message->to($request->manager_email)->subject($subject);
                        $message->from($request->from);
                    });
                }
            }
        } else {
            $attachment = [];
            if($request->hasFile('file')){
                $attachment = $request->File('file');
            }

            Mail::send('email', $data, function ($message) use ($request, $subject, $attachment)
            {
                $message->to($request->to)->subject($subject);
                $message->from($request->from);
                
                foreach ($attachment as $file){
                    $message->attach($file, ['as' => $file->getClientOriginalName()]);
                }
            });
                
            $userData = Employees::from('employees as em')
                ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
                ->where('ur.email', '=', $request->from)
                ->get(['em.id', 'em.empid', 'ur.name', 'ur.email']);

            $subject = 'The PORTAL: We got your mail.';    

            $data = array(
                'name' => $userData[0]['name']
            );

            Mail::send('hrautoreply', $data, function ($message) use ($request, $subject)
            {
                $message->to($request->from)->subject($subject);
                $message->from($request->to);
            });
        }
        
        if (Mail::failures()) {
            return new Error(Mail::failures()); 
        }
    
        return response('success', 200);
    }
}
