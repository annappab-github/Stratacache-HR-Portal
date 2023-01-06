<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\EmployeeGradeController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\EmployeeStatusController;

// use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/athenticated', function () {
    return true;
});

Route::middleware(['auth:sanctum'])->group(function(){

    Route::post('logout', [LoginController::class, 'logout']);

      Route::post('/downloadReport', [ReportController::class, 'generateReport']);

      Route::post("/postRole", [RoleController::class,'store']);
      Route::post("/deleteRole/{id}", [RoleController::class,'delete']);
      Route::get("/getRole/{id}", [RoleController::class,'getOne']);
      Route::put("/editRole/{id}", [RoleController::class,'update']);
      Route::post("/store", [PermissionController::class,'store']);
      Route::get("/getAllRoles", [RoleController::class,'getAll']);
      Route::get("/getAllPermissions", [PermissionController::class,'getAll']);
      Route::get("/getRolePermissions/{id}", [PermissionController::class,'getRolePermission']);

      Route::get("/getAllUsers", [UserController::class,'getAll']);
      Route::post('/account/create', [UserController::class,'store']);
      Route::put('/account/update', [UserController::class,'update']);
      Route::put('/changepassword/{id}', [UserController::class,'changePassword']);
      Route::put('/passwordChange', [UserController::class,'forceChangePassword']);
      Route::get('/account/{id}',[UserController::class,'getOne']);
      Route::get('/getUser',[UserController::class,'getCurrent']);
      Route::delete('/delete/user/{id}', [UserController::class,'delete']);

      Route::get('audit', [AuditController::class, 'getAll']);    
      Route::post('audit', [AuditController::class, 'createAuditForFiles']);

    //   Route::get('employees', EmployeeController::GET_ALL_EMPLOYEES_DATA_FROM_EXCEL);            
      Route::post('home', EmployeeController::GET_EMPLOYEE_DASHBOARD_DATA);
      Route::post('myInfo', EmployeeController::GET_EMPLOYEE_INFO_DATA);
      Route::post('myInfo/updateProfilePhoto', EmployeeController::UPDATE_EMPLOYEE_PROFILE_PHOTO);
      Route::post('leaves', EmployeeController::GET_EMPLOYEE_LEAVE_DATA);

      Route::get('allemployees', EmployeesController::GET_ALL_EMPLOYEES);
      Route::get('employeeinfo/{empid}', EmployeesController::GET_EMPLOYEE_INFO);
      Route::get('employeeinfobyemail/{email}', EmployeesController::GET_EMPLOYEE_INFO_BY_EMAIL);
      Route::put('updateemployee', EmployeesController::UPDATE_EMPLOYEE);
      Route::get('employeesidforreporting', EmployeesController::GET_EMPLOYEES_DATA_FOR_REPORTING);

      Route::get('files', FileController::GET_ALL_FILES);
      Route::post('files/download', FileController::DOWNLOAD_FILE_DOCUMENTS);
      Route::post('files/upload', FileController::UPLOAD_FILE);
      Route::get('files/allUpload', FileController::GET_ALL_FOLDERS_SUBFOLDERS_FILES);
      Route::post('files/downloadAll', FileController::DOWNLOAD_FILE_ALL);

      Route::get('cityAndCountries', CitiesController::GET_CITIES_AND_COUNTRIES);
      
      Route::get('getAllDepertments', DepartmentsController::GET_ALL_DEPERMENTS);

      Route::get('getAllGrades', EmployeeGradeController::GET_ALL_GRADES);
      
      Route::get('getAllEmploymentTypes', EmploymentTypeController::GET_ALL_EMPLOYEMENT_TYPES);
      
      Route::get('getAllStatus', EmployeeStatusController::GET_ALL_STATUS);

});

Route::post('email', EmailController::SEND_EMAIL);

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::post('forgotPassword', EmailController::SEND_EMAIL);
Route::put('/forgotPasswordReset', [UserController::class,'forgotPasswordReset']);

Route::get('/forgotUser/{email}',[UserController::class,'getUser']);

// Route::post('reset-password', AuthController::class,'sendPasswordResetLink');
// Route::post('reset/password', AuthController::class,'callResetPassword');
// Route::get('leaves', [EmployeeController::class, 'employeeLeaveDetails']); 