<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\HostelFeeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuPermissionController;
use App\Http\Controllers\MiscellaniousController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionHeadController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StudentBillController;
use App\Http\Controllers\StudentBillPaymentController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\SessionsController;
use \App\Http\Controllers\YearController;
use \App\Http\Controllers\SettingController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\CommonController;
use \App\Http\Controllers\SupportController;
use \App\Http\Controllers\SessionFeeController;

Route::post('login', [AuthController::class, 'login']);

//for customer login
Route::post('auth/login', [CustomerAuthController::class, 'login']);
Route::post('auth/registration', [CustomerAuthController::class, 'registration']);
Route::get('auth/user', [CustomerAuthController::class, 'me']);
Route::post('auth/logout', [CustomerAuthController::class, 'logout']);

Route::group(['middleware' => 'jwt:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::get('app-supporting-data', [SettingController::class, 'appSupportingData']);
});

Route::group(['middleware' => ['jwt:api']], function () {
    // ADMIN USERS
    Route::apiResource('users',UserController::class);
    Route::get('search/users/{query}', [UserController::class,'search']);
    Route::get('get-all-users/', [UserController::class, 'getAllUser']);
    //Head MODULE
    Route::group(['prefix' => 'head'],function () {
        Route::post('list',[HeadController::class,'index']);
        Route::post('create',[HeadController::class,'store']);
        Route::post('update/{id}',[HeadController::class,'update']);
        Route::get('by-id/{id}',[HeadController::class,'byId']);
    });

    Route::apiResource('student',StudentController::class);
    Route::get('search/student/{query}', [StudentController::class,'search']);
    Route::get('get-schedule-data/{session_id}/{category_id}', [StudentController::class,'getScheduleData']);
    Route::get('get-student-wise-schedule-data/{student_id}', [StudentController::class,'getStudentWiseScheduleData']);
    Route::post('student-details', [StudentController::class,'getStudentDetails']);

//Slider
    Route::apiResource('sliders',SliderController::class);
    Route::get('search/sliders/{query}', [SliderController::class,'search']);

    //event
    Route::apiResource('events',EventController::class);
    Route::get('search/events/{query}', [EventController::class,'search']);

    //program
    Route::apiResource('programs',ProgramController::class);
    Route::get('search/programs/{query}', [ProgramController::class,'search']);
    Route::get('user-programs', [ProgramController::class,'userProgram']);
    //customer
    Route::apiResource('customers',CustomerController::class);
    Route::get('search/customers/{query}', [CustomerController::class,'search']);

    //partner
    Route::apiResource('partners',PartnerController::class);
    Route::get('search/partners/{query}', [PartnerController::class,'search']);
    //instructor
    Route::apiResource('instructors',InstructorController::class);
    Route::get('instructor-details/{id}',[InstructorController::class,'show']);
    Route::get('search/instructors/{query}', [InstructorController::class,'search']);

    //menu resource route
    Route::apiResource('menu', MenuController::class);
    Route::get('search/menu/{query}', [MenuController::class,'search']);
    Route::get('get-all-menu', [MenuController::class,'getAllMenu']);

    //menu permission route
    Route::get('get-user-menu-details/{UserID}', [MenuPermissionController::class, 'getUserMenuPermission']);
    Route::get('sidebar-get-all-user-menu', [MenuPermissionController::class,'getSidebarAllUserMenu']);
    Route::post('save-user-menu-permission', [MenuPermissionController::class,'saveUserMenuPermission']);

    Route::get('get-all-session', [CommonController::class,'getAllSession']);
});

Route::get('get-all-slider', [FrontController::class,'getAllSlider']);
Route::get('get-all-program', [FrontController::class,'getAllProgram']);
Route::get('get-all-events', [FrontController::class,'getAllEvents']);
Route::get('get-program-details', [FrontController::class, 'getOurProgramDetails']);

Route::group(['middleware' => 'CustomerAuth'], function () {
    Route::post('auth/profile-update', [CustomerAuthController::class, 'updateProfile']);
    Route::get('join-program', [ActivityController::class, 'joinProgram']);
});
