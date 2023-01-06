<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        
        .pdf-logo {
            width: 180px;
            height: 52px;
            margin: 0 auto;
        }
        
        .pdf-logo img {
            width: 180px;
            height: 52px;
        }
        
        .report-dt {
            text-align: center;
            font-size: 15px;
            margin: 25px auto 10px;
        }
        
        .report-title {
            text-align: center;
            font-size: 15px;
            margin: 10px 0;
            font-weight: bold;
        }
        
        .pdf-info .flex-row table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        .pdf-info .flex-row table tr {
            height: 20px;
        }
        
        .f-bold {
            font-weight: bold;
        }
        
        .hr-line {
            margin: 7px 0 10px;
            /* border-color: black; */
            border-top: 1px solid black;
            height: 1px;
        }
        
        .tableData {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tableData td,
        .tableData th {
            border: 1px solid #000;
            text-align: left;
            padding: 3px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="pdf-body">
        <div class="pdf-logo">
            <img src="{{ public_path('scala_logo.png') }}" alt="">
        </div>
        <div class="pdf-info">
            <p class="report-dt">{{ $reportTitle }}</p>
            <p class="report-title">{{ $reportName }}</p>
            <div class="flex-row">
                <table>
                    <tbody>
                        <tr>
                        <td class="f-bold" style="width: 18%;">Date Range:</td>
                            <td style="width: 57%">{{ $fromDate }} To {{ $toDate }}</td>
                            <td style="width: 10%"></td>
                        </tr>
                        <tr>
                        <td class="f-bold" style="width: 18%;">Department:</td>
                        <td style="width: 27%">
                        <?php $departmentcount = 0; ?>
                    @foreach($department as $data)
                        <?php $departmentcount = $departmentcount + 1; ?>
                            <span>{{ $data }} {{count($department) != $departmentcount ? ', ' : ''}}</span>
                    @endforeach
                        </td>
                        </tr>
                        <tr>
                        <td class="f-bold" style="width: 18%;">Status:</td>
                        <td style="width: 27%">
                        <?php $statuscount = 0; ?>
                    @foreach($status as $data)
                        <?php $statuscount = $statuscount + 1; ?>
                            <span>{{ $data }} {{count($status) != $statuscount ? ', ' : ''}}</span>
                    @endforeach
                        </td>
                        </tr>
                        <tr>
                        <td class="f-bold" style="width: 18%;">Location:</td>
                        <td style="width: 27%">
                        <?php $locationcount = 0; ?>
                    @foreach($location as $data)
                        <?php $locationcount = $locationcount + 1; ?>
                            <span>{{ $data }} {{count($location) != $locationcount ? ', ' : ''}}</span>
                    @endforeach
                        </td>
                        </tr>
                        <tr>
                        <td class="f-bold" style="width: 18%;">Employee Type:</td>
                        <td style="width: 27%">
                        <?php $typecount = 0; ?>
                    @foreach($employement_type as $data)
                        <?php $typecount = $typecount + 1; ?>
                            <span>{{ $data }} {{count($employement_type) != $typecount ? ', ' : ''}}</span>
                    @endforeach
                        </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="hr-line"></div>
        <table class="tableData">
            <thead>
                <tr>
                    @foreach($reportHeader as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $data)
                <tr>
                    <td>{{ $data->empid }}</td>
                    <td>{{ $data->start_Date }}</td>
                    <td>{{ $data->employee_grade }}</td>
                    <td>{{ $data->name }}</td>
                    <td>{{ $data->location }}</td>
                    <td>{{ $data->city }}</td>
                    <td>{{ $data->position }}</td>
                    <td>{{ $data->department }}</td>       
                    <td>{{ $data->land_ext }}</td>
                    <td>{{ $data->mobile_number }}</td>
                    <td>{{ $data->email }}</td>
                    <td>{{ $data->reporting_to }}</td>
                    <td>{{ $data->birth_dates }}</td>
                    <td>{{ $data->probation_period }}</td>
                    <td>{{ $data->confirmation_date }}</td>
                    <td>{{ $data->remarks }}</td>
                    <td>{{ $data->new_confirmationdate }}</td>
                    <td>{{ $data->employement_type }}</td>
                    <td>{{ $data->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>