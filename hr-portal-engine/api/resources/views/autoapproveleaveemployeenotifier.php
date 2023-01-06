<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, Helvetica, sans-serif;
			}
			
			a {
				text-decoration: none;
			}
			
            .resetLink {
                background-color: #122E55;
                color: white;
				width: 150px;
				height: 30px;
				font-size: 15px;
            }
        </style>
    </head>
    <body>
        <p> 
            Hi <?=$employee_name?>, <br><br>
			
            You have been allocated time off request for <?=$total_days?> day(s) of <?=$type_of_leave?> from <?=$from?> to <?=$to?><?=$reason?>. <br><br>

            Regards,<br>
            The Portal
        </p>    
    </body>
</html>