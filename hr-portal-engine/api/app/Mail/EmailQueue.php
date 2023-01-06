<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;
use Carbon\Carbon;
use Storage;

class EmailQueue extends Mailable
{
    use Queueable, SerializesModels;
    protected $requestDetails;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->requestDetails = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $requestType = $this->requestDetails['type'];

        if($requestType == "Reset Password Notification") {
            $subject = $requestType.' : '.$this->requestDetails['subject'];
            return $this->from($this->requestDetails['from'], 'thePortal')
                        ->subject($subject)
                        ->view('forgotpasswordemail')->with($this->requestDetails);
        } else if($requestType == "New Employee") {
            $subject = 'Welcome to THE PORTAL, you are now connected.';
            return $this->from($this->requestDetails['from'], 'thePortal')
                        ->subject($subject)
                        ->view('newemployeewelcome')->with($this->requestDetails);
        } else if($requestType == "New Reportee") {
            $subject = 'Hi Line Manager, You have a new team member.';
            return $this->from($this->requestDetails['from'], 'thePortal')
                        ->subject($subject)
                        ->view('newemployeemanagernotifier')->with($this->requestDetails);
        } else if($requestType == "Apply Leave") {
            $subject = 'Leave Application Request';
            return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('applyleavemanagernotifier')->with($this->requestDetails);
        } else if($requestType == "Approved Leave") {
            $subject = 'Your time off has a response';
            if($this->requestDetails['autoApproved']) {
                return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('autoapproveleaveemployeenotifier')->with($this->requestDetails);
            } else {
                return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('approveleaveemployeenotifier')->with($this->requestDetails);
            }
        } else if($requestType == "Rejected Leave") {
            $subject = 'Your time off has a response';
            return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('rejectleaveemployeenotifier')->with($this->requestDetails);
        } else if($requestType == "Cancelled Leave") {
            $subject = 'Leave Request Cancelled';
            if($this->requestDetails['for'] == 'requestor') {
                return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('cancelleaveemployeenotifier')->with($this->requestDetails);
            } else {
                return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('cancelleavemanagernotifier')->with($this->requestDetails);
            }
            
        } else if($requestType == "Accrual Success") {
            $subject = '[Success] APAC Leave Accrual '.$this->requestDetails['accrual_year'];
            $accrual = $this->from($this->requestDetails['fromAdd'], 'thePortal')
                        ->subject($subject)
                        ->view('accrualsuccess')->with($this->requestDetails);
              
            $fileName_before = "Before_Accrual_".Carbon::now()->year.'.'.$this->requestDetails['fType'];         
            $filePath_before = Storage::disk('local')->path($fileName_before);            
            $accrual->attach($filePath_before, [
                'as' => $fileName_before,
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);  
            
            $fileName_after = "After_Accrual_".Carbon::now()->year.'.'.$this->requestDetails['fType'];         
            $filePath_after = Storage::disk('local')->path($fileName_after);
            $accrual->attach($filePath_after, [
                'as' => $fileName_after,
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]); 
            return $accrual;
        } else if($requestType == "Accrual Failed") { 
            $subject = '[Failed] APAC Leave Accrual '.$this->requestDetails['accrual_year'];
            return $this->from($this->requestDetails['fromAdd'], 'thePortal')
                    ->subject($subject)
                    ->view('accrualfailure')->with($this->requestDetails);
        } else {
            $subject = '[The Portal] : '.$this->requestDetails['subject'];
            $from = $this->requestDetails['from'] == null ? 'anonymous@stratacache.com' : $this->requestDetails['from'];

            $writeToUs = $this->from($from)
                        ->subject($subject)
                        ->view('email')->with($this->requestDetails);
            
            $attachments = $this->requestDetails['attachedFiles']; 
            if(count($attachments) > 0) {
                foreach ($attachments as $filePath => $fileParameters){
                    $writeToUs->attach($filePath, $fileParameters);
                }
            }         
            
            return $writeToUs;
        }
    }
}
