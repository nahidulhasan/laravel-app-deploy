<?php

use App\Http\Controllers\API\V1\ComplianceEntryController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\V1\PeriodicTicketController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\UserRolesController;
use App\Http\Controllers\API\V1\MailReminderController;
use App\Http\Controllers\API\V1\CeoCxoReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


//Route::group(['middleware' => ['verify.secret']], function () {

Route::group(['prefix' => 'v1'], function () {

    // Compliance Entry Routes
    Route::controller(ComplianceEntryController::class)->group(function () {
        Route::POST('compliance-entry', 'store');
        Route::PUT('next-due-date', 'updateNextDueDate');
        Route::POST('calculate-next-due-date', 'calculateNextDueDate');
        Route::GET('next-quarter-date', 'getNextQuarterDate');
        Route::GET('compliance-point-no/{regulatoryBody}', 'compliancePointNo')->where('regulatoryBody', '[A-Za-z]+');
        Route::POST('check-compliance-point-no', 'checkCompliancePointNo');
        Route::POST('update-fields-compliance-entry', 'updateFields');
    });

    // Periodic Entry Routes
    Route::controller(PeriodicTicketController::class)->group(function () {
        Route::GET('periodic-ticket', 'createPeriodicTicket')->name('periodicTicket');
        Route::POST('periodic-ticket-status-update', 'periodicTicketStatus');
        Route::PUT('periodic-ticket-due-date', 'updatePeriodicTicketDueDate');
        Route::GET('token', 'getToken');
        Route::GET('token-info', 'getTokenInfo');
        Route::POST('token-info', 'getTokenInfo');
        Route::GET('update-ticket-id', 'updatePeriodicTicketTicketId');
        Route::GET('period/{dueDate}/{frequency}', 'period');
    });

    // Category Routes
    Route::controller(CategoryController::class)->group(function () {
        Route::get('category', 'index');
    });

    // user routes
    Route::controller(UserController::class)->group(function () {
        Route::get('user-dropdown', 'getUserDropdown');
    });

    // user role routes
    Route::controller(UserRolesController::class)->group(function () {
        Route::POST('user-role', 'getUserRole');
        Route::get('user-role/{email?}', 'getUserRole');
        Route::get('user-role-sync', 'sync');
        Route::get('user-role-sync-api', 'syncApi');
        Route::get('get-role-users/{type}/{email?}', 'getRoleUserByType');
        Route::POST('get-role-tickets', 'getRoleTicketsType');
    });

    // Reminder Mail Routes
    Route::controller(MailReminderController::class)->group(function () {
        Route::get('mail-reminder', 'index');
        Route::get('cxo-email-reminder', 'cxoEmailReminder');
        Route::get('ceo-email-reminder', 'ceoEmailReminder');
        Route::get('emt-email-reminder', 'emtEmailReminder');
    });

    // CEO CXO reports
    Route::controller(CeoCxoReportController::class)->group(function () {
        Route::get('ceo-cxo-report', 'index');
        Route::get('ceo-cxo-report/{id}', 'show');
        Route::POST('update-email-status', 'updateEmailStatus');
    });

    // Dashboard Routes 
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard/{userId}', 'index');
        Route::post('cxo-ceo-details', 'verifyCxoCeoUser');
        Route::post('dashboard-tickets', 'getDashboardTickets');
        Route::post('dashboard-reports', 'getDashboardReports');
    });

    Route::namespace('\Rap2hpoutre\LaravelLogViewer')->group(function () {
        Route::get('logs', 'LogViewerController@index');
    });

    Route::get('/get-current-branch', function () {
        $output = [];
        $branch = exec('awk -F"/" \'/ref: refs\/heads\//{print $NF}\' /var/www/html/.git/HEAD');
        return response()->json(['message' => $branch]);
    });
});

//});

