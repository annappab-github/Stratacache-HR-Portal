<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\application_job;
use Carbon\Carbon;

class ApplicationJobController extends Controller
{
    public function createJob($jobName, $jobDescription) {
        $appJob = new application_job;
        $appJob->job_name = $jobName;
        $appJob->job_description = $jobDescription;
        $appJob->job_status = 'Running';
        $appJob-> save();

        return $appJob;
    }

    public function updateJob($jobDetails) {
        $appJob = application_job::find($jobDetails->id);
        $appJob->job_status = $jobDetails->job_status;
        $appJob->exception = $jobDetails->exception;
        $appJob->save();
    }

    public function getJobById($Id) {
        $appJob = application_job::find($id);
        return $appJob;
    }

    public function checkIfAnyJobPresentByName($jobName) {
        $jobByName = application_job::from('application_jobs as aj')
        ->whereYear('aj.created_at', Carbon::now()->year)
        ->where('aj.job_name', $jobName)
        ->get();

        if(count($jobByName) == 0){
            return false;
        }

        return true;
    }
}
