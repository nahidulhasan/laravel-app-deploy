<?php

use App\Http\Controllers\ClientCredentialController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ComplianceEntryController;
use App\Http\Controllers\PeriodicTicketController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserRolesController;
use App\Http\Controllers\NotificationConfigController;
use App\Http\Controllers\CxoNotificationConfigController;
use App\Http\Controllers\CeoCxoReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store']);

Route::get('/dashboard', [ComplianceEntryController::class, 'index'])->middleware(['auth'])->name('dashboard');


Route::get('index', [ClientCredentialController::class, 'index'])->name('index');
Route::get('compliance-entry', [ComplianceEntryController::class, 'index'])->name('compliance-entry');
Route::get('compliance-entry-view/{id}', [ComplianceEntryController::class, 'view'])->name('compliance-entry-view');
Route::get('periodic-tickets', [PeriodicTicketController::class, 'index'])->name('periodic-tickets');
Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
Route::get('activity-logs-view/{id}', [ActivityLogController::class, 'view'])->name('activity-logs-view');

Route::get('edit/{id}', [ClientCredentialController::class, 'edit'])->name('edit');
Route::put('update-token/{id}', [ClientCredentialController::class, 'update'])->name('update-token');

Route::get('create', [ClientCredentialController::class, 'create'])->name('create');
Route::post('create', [ClientCredentialController::class, 'store'])->name('create-token');

Route::resource('notification-configs', NotificationConfigController::class);
Route::resource('cxo-notification-configs', CxoNotificationConfigController::class);
Route::resource('ceo-cxo-report', CeoCxoReportController::class);

// user role routes
Route::controller(UserRolesController::class)->group(function () {
    Route::get('user-role', 'index')->name('user-role');
    Route::get('user-role-sync', 'sync')->name('user-role-sync');
    Route::get('user-role/create', 'create')->name('user-role-create');
    Route::get('user-role/edit/{id}', 'edit')->name('user-role-edit');
    Route::delete('user-role/{id}', 'destroy')->name('user-role-delete');
    Route::post('user-role', 'store')->name('user-role-store');
    Route::put('user-role/{id}', 'update')->name('user-role-update');

});

// user routes
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index')->name('users');
    Route::get('users-sync', 'sync')->name('users-sync');
    Route::get('users/create', 'create')->name('users-create');
    Route::get('users/edit/{id}', 'edit')->name('users-edit');
    Route::delete('users/{id}', 'destroy')->name('users-delete');
    Route::post('users', 'store')->name('users-store');
    Route::put('users/{id}', 'update')->name('users-update');

});


// user routes
/*Route::controller(RegistrationController::class)->group(function () {
    Route::get('insert-dummy', 'insertDummy');
    Route::get('users-sync', 'sync')->name('users-sync');
    Route::get('users/create', 'create')->name('users-create');
    Route::get('users/edit/{id}', 'edit')->name('users-edit');
    Route::delete('users/{id}', 'destroy')->name('users-delete');
    Route::post('users', 'store')->name('users-store');
    Route::put('users/{id}', 'update')->name('users-update');

});*/


require __DIR__ . '/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
