<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AL-SL</title>
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
        <table class="tableData">
            <thead>
                <tr>
                    @foreach($reportHeader as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $key => $data)
                <tr>
                    <td>{{ $data['country'] }}</td>
                    <td>{{ $data['name'] }}</td>
                    <td>{{ $data['abal'] }}</td>
                    <td>{{ $data['sbal'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>