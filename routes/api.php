<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\HostelFeeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuPermissionController;
use App\Http\Controllers\MiscellaniousController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionHeadController;
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

    Route::group(['prefix' => 'support'],function () {
        Route::get('session-years-data',[SupportController::class,'index']);
        Route::get('session-head-data',[SupportController::class,'sessionHeadData']);
    });

    //SESSIONS MODULE
    Route::group(['prefix' => 'sessions'],function () {
        Route::post('list',[SessionsController::class,'index']);
        Route::post('create',[SessionsController::class,'store']);
        Route::post('update/{id}',[SessionsController::class,'update']);
        Route::get('by-id/{id}',[SessionsController::class,'byId']);
    });
    //YEARS MODULE
    Route::group(['prefix' => 'years'],function () {
        Route::post('list',[YearController::class,'index']);
        Route::post('create',[YearController::class,'store']);
        Route::post('update/{id}',[YearController::class,'update']);
        Route::get('by-id/{id}',[YearController::class,'byId']);
    });

    //SESSION FEES MODULE
    Route::apiResource('session-fees',SessionFeeController::class);
    Route::get('search/session-fees/{query}', [SessionFeeController::class,'search']);

    //STUDENTS MODULE
    Route::group(['prefix' => 'students'],function () {
        Route::post('list',[SessionFeeController::class,'index']);
        Route::post('create',[SessionFeeController::class,'store']);
        Route::post('update/{id}',[SessionFeeController::class,'update']);
        Route::get('by-id/{id}',[SessionFeeController::class,'byId']);
    });

    //Head MODULE
    Route::group(['prefix' => 'head'],function () {
        Route::post('list',[HeadController::class,'index']);
        Route::post('create',[HeadController::class,'store']);
        Route::post('update/{id}',[HeadController::class,'update']);
        Route::get('by-id/{id}',[HeadController::class,'byId']);
    });

    //Session Head MODULE
    Route::group(['prefix' => 'session-head'],function () {
        Route::post('list',[SessionHeadController::class,'index']);
        Route::post('create',[SessionHeadController::class,'store']);
        Route::post('update/{id}',[SessionHeadController::class,'update']);
        Route::get('by-id/{id}',[SessionHeadController::class,'byId']);
    });

    Route::apiResource('student',StudentController::class);
    Route::get('search/student/{query}', [StudentController::class,'search']);
    Route::get('get-schedule-data/{session_id}/{category_id}', [StudentController::class,'getScheduleData']);
    Route::get('get-student-wise-schedule-data/{student_id}', [StudentController::class,'getStudentWiseScheduleData']);
    Route::post('student-details', [StudentController::class,'getStudentDetails']);

    Route::apiResource('student-bill',StudentBillController::class);
    Route::get('search/student-bill/{query}', [StudentBillController::class,'search']);
    Route::get('filter-student-wise-schedule', [StudentBillController::class,'filterStudentWiseSchedule']);
    Route::get('export-student-wise-schedule/{student_id}/{query}', [StudentBillController::class,'exportStudentWiseSchedule']);
    Route::post('session-wise-student', [StudentBillController::class,'sessionWiseStudent']);
    Route::post('session-roll-wise-student', [StudentBillController::class,'sessionRollWiseStudent']);

    Route::apiResource('categories',CategoryController::class);
    Route::get('search/categories/{query}', [CategoryController::class,'search']);

    Route::apiResource('miscellanious',MiscellaniousController::class);
    Route::get('search/miscellanious/{query}', [MiscellaniousController::class,'search']);
    Route::get('miscellaneous-invoice/{id}', [MiscellaniousController::class,'miscellaneousInvoice']);

    Route::apiResource('bank',BankController::class);
    Route::get('search/bank/{query}', [BankController::class,'search']);

    Route::apiResource('branch',BranchController::class);
    Route::get('search/branch/{query}', [BranchController::class,'search']);
    Route::post('get-account-by-branch', [BranchController::class,'getAccountNoByBranch']);

    Route::apiResource('hostel-fee',HostelFeeController::class);
    Route::get('search/hostel-fee/{query}', [HostelFeeController::class,'search']);
    Route::get('hostel-fee-invoice/{id}', [HostelFeeController::class,'hostelFeeInvoice']);
    Route::get('hostel-student-list', [HostelFeeController::class,'hostelStudentList']);

    //STUDENTS Payment Bill MODULE
    Route::apiResource('student-payment-bill', StudentBillPaymentController::class);
    Route::put('update-student-payment-bill', [StudentBillPaymentController::class,'update']);
    Route::get('search/student-payment-bill/{query}', [StudentBillPaymentController::class,'search']);
    Route::post('student-payment-bill/check-amount',[StudentBillPaymentController::class,'checkAmount']);
    Route::post('student-payment-bill/check-amount-for-edit',[StudentBillPaymentController::class,'checkAmountForEdit']);
    Route::get('export-student-payment-bill',[StudentBillPaymentController::class,'studentPaymentBillDataExport']);
    Route::get('student-payment-bill-details/{id}',[StudentBillPaymentController::class,'details']);
    Route::get('student-bill-payment-invoice/{id}',[StudentBillPaymentController::class,'billInvoice']);

    Route::group(['prefix' => 'report'],function () {
         Route::get('student-payment',[ReportController::class,'studentPayment']);
         Route::get('student_payment_report_details/{roll_no}/{batch_number}',[ReportController::class,'studentPaymentReportDetails']);
         Route::get('head-wise-student-payment-report',[ReportController::class,'headWisePaymentReport']);
         Route::get('head_wise_payment_report_print/{sessionId}/{roll_no}/{batch_number}',[ReportController::class,'headWisePaymentReportPrint']);
         Route::get('student-report-print/',[ReportController::class,'studentReportPrint']);
         Route::get('student-individual-payment-report/',[ReportController::class,'studentIndividualReport']);
         Route::get('export-student-payment-report',[ReportController::class,'studentPaymentReportExport']);
         //hostel bill report route
         Route::get('student-hostel-bill-report',[ReportController::class,'studentHostelBillReport']);
         Route::get('export-student-head-wise-payment-report',[ReportController::class,'ExportStudentHeadWisePaymentReport']);
         Route::get('student-hostel-report-details/{roll_no}/{batch_number}',[ReportController::class,'studentHostelBillReportDetails']);
         Route::get('export-student-hostel-bill-report',[ReportController::class,'studentHostelBillReportExport']);
    });


    //menu resource route
    Route::apiResource('menu', MenuController::class);
    Route::get('search/menu/{query}', [MenuController::class,'search']);
    Route::get('get-all-menu', [MenuController::class,'getAllMenu']);

    //menu permission route
    Route::get('get-user-menu-details/{UserID}', [MenuPermissionController::class, 'getUserMenuPermission']);
    Route::get('sidebar-get-all-user-menu', [MenuPermissionController::class,'getSidebarAllUserMenu']);
    Route::post('save-user-menu-permission', [MenuPermissionController::class,'saveUserMenuPermission']);

    Route::get('get-all-session', [CommonController::class,'getAllSession']);
    Route::get('get-all-student', [CommonController::class,'getAllStudent']);
    Route::get('get-all-currency', [CommonController::class,'getAllCurrency']);
    Route::get('get-all-category', [CommonController::class,'getAllCategory']);
    Route::get('get-all-role', [CommonController::class,'getAllRole']);
    Route::get('get-all-bank', [CommonController::class,'getAllBank']);
    Route::get('get-all-head', [CommonController::class,'getAllHead']);
    Route::get('get-mro-no', [CommonController::class,'getMroNo']);
    Route::get('get-all-payment-method', [CommonController::class,'getAllPaymentMethod']);
    Route::post('get-roll-wise-student', [CommonController::class,'getRollWiseStudent']);
    Route::get('get-all-bank-wise-branch/{bank_id}', [CommonController::class,'getBankWiseBranches']);
    Route::get('session-wise-batch/{session_id}', [CommonController::class,'sessionWiseBatch']);
    Route::get('get-all-purpose', [CommonController::class,'getAllPurpose']);
});
