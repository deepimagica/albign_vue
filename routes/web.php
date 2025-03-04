<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('/', function () {
//     return Inertia::render('Auth/LoginForm'); 
// });

Route::middleware('guest.user')->group(function () {
    Route::get('/', [LoginController::class, 'getLoginPage'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth.user')->group(function () {
    Route::match(['get', 'post'],'dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');
    Route::get('user/agreement/{doctor_id}', [DashboardController::class, 'getAgreementPage'])->name('agreement');
    Route::post('user/agreement/{doctor_id}', [DashboardController::class, 'storeAgreementData'])->name('post.agreement');
    Route::get('user/survey/{doctor_id}',[DashboardController::class,'getSurveyPage'])->name('get.survey');

});