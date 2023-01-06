<?php

namespace App\Exports;

use App\Invoice;
use App\Models\Gate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ReportController;

class CsvExcelExport implements FromView
{
    public function view(): View
    {   
        $input = request()->all();
        $request = new Request($input);
        $reportControllerObject = new ReportController();

        switch($request->rType) {
            case('gate'):
                return view('gates', $reportControllerObject->getGateReportData($request));

            case('audit'):
                return view('audits', $reportControllerObject->getAuditReportData($request));
                
            case('employee'):
                return view('employees', $reportControllerObject->getEmployeeReportData($request));    
        }        
    }
}