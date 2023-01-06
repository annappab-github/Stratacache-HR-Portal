<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
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
            Hello! <br><br>
        
            You are receiving this email because we received a password reset request for your account. <br><br>

            
            <a href="<?=$url?>"><button class="resetLink">Reset Password</button></a><br><br>
			
			
            If you did not request a password reset, no further action is required. <br><br>

            Regards,<br>
            Stratacache Team
        </p>    
    </body>
</html>