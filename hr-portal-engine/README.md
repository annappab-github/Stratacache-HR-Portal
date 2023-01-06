# HR Portal Engine
Backend for HR Portal

# update the packages
composer update

# run the migrations
php artisan migrate

# run the seed to populate with sample data
php artisan db:seed

# start the server
php artisan serve


# Audit related changes
Go to Vendor>spatie>Models>role.php and add:  
use OwenIt\Auditing\Contracts\Auditable;  
implements Auditable;  
use \OwenIt\Auditing\Auditable;  


# For Production build execute below to optimize the code

APP_ENV=production  
APP_DEBUG=false  

composer dump-autoload --optimize  

php artisan config:cache  

php artisan route:cache  

php artisan view:cache  

php artisan optimize  

npm run production  

# To deploy laravel project on apache.

Copy all the contents inside api folder

Navigate to the htdocs inside apache folder

Create a new folder (NOTE: this foldername will be used in the API url eg: 10.10.2.15/HR_Engine/api/.... for our reference let's name it HR_Engine)

Navigate inside the new folder and make another folder (NOTE: for our reference let's call it Engine)

Navigate inside the Engine folder and paste the contents from the api folder 

Now navigate inside the public folder and move the content from public to the root folder (i.e HR_Engine)

Open the index.php file and edit the paths

Example: 

if (file_exists(__DIR__.'/Engine/storage/framework/maintenance.php')) {
    require __DIR__.'/Engine/storage/framework/maintenance.php';
}

Now the laravel project is deployed on 10.10.2.15/HR_Engine