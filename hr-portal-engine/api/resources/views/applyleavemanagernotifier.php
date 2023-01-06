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
			
            .link {
                background-color: #d05959;
                color: white;
				width: 150px;
				height: 30px;
				font-size: 15px;
                border: 0px;
                margin-left: 200px;
            }
        </style>
    </head>
    <body>
        <p> 
            Hi <?=$manager_name?>, <br><br>
			
            <?=$employee_name?> would like to apply for <?=$type_of_leave?> from <?=$from_date?> to <?=$to_date?><?=$reason?>. <br><br>

            Request your approval.<br><br>

            <a href="<?=$url?>"><button class="link">Login to The Portal</button></a><br><br>

            Regards,<br>
            The Portal
        </p>    
    </body>
</html>