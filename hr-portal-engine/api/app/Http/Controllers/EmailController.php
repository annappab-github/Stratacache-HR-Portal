<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mail;
use File;
use Storage;
use App\Models\Employees;
use App\Jobs\SendEmailJob;

class EmailController extends Controller
{
 
    const SEND_EMAIL = [self::class, 'sendEmail'];
    
    public function sendEmail(Request $request) {
        $receivedFiles = [];
        $attachments = [];
        
        if($request->hasFile('file')){
            $receivedFiles = $request->File('file');
        }

        if(count($receivedFiles) > 0) {
            foreach ($receivedFiles as $file){
                $fileName = $file->getClientOriginalName();
                $fileMimeType = $file->getMimeType();
                Storage::disk("local")->put($fileName, file_get_contents($file));
                $filePath = Storage::disk('local')->path($fileName);
                
                $attachments[$filePath] = ['as' => $fileName, 'mime' => $fileMimeType];
            }
        }    

        $request->merge(["attachedFiles" => $attachments ]);

        SendEmailJob::dispatch($request->except('file'));

    }
}
