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
    Route::post('survey/store-answer', [DashboardController::class, 'storeAnswer'])->name('survey.storeAnswer');
    Route::post('survey/previous-answer', [DashboardController::class,'getPreviousAnswer'])->name('survey.getPreviousAnswer');
    Route::get('user/confirmation/{doctor_id}', [DashboardController::class, 'getConfirmationPage'])->name('confirmation');
    Route::post('user/confirmation/{doctor_id}', [DashboardController::class, 'storeConfirmationData'])->name('post.confirmation');
    Route::get('user/account-details/{doctor_id}', [DashboardController::class, 'getAccountDetailPage'])->name('accountDetail');
    Route::post('user/account-details/{doctor_id}', [DashboardController::class, 'storeAccountDetails'])->name('post.accountDetail');
    Route::get('user/signature/{doctor_id}', [DashboardController::class, 'getSignaturePage'])->name('signature.page');
    Route::post('user/signature/{doctor_id}', [DashboardController::class, 'verifySignature'])->name('verify.signature');
    Route::get('user/verify-mobile/{doctor_id}', [DashboardController::class, 'getVerifyPage'])->name('verify.mobile');
    Route::post('user/verify-mobile/{doctor_id}', [DashboardController::class, 'verifyOTP'])->name('verify.otp');
    Route::post('/resend-otp/{doctor_id}', [DashboardController::class, 'resendOtp'])->name('resendOtp');
    Route::get('user/survay-complete/{survey_id}/{doctor_id}', [DashboardController::class, 'getSurveyFinalPage'])->name('survey.final');

});