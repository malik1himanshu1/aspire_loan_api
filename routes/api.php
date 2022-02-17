<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Pre login APIs
Route::post( 'register', [ UserController::class, 'createUser' ] );
Route::post( 'login', [ UserController::class, 'checkUser' ] );

// Post Login APIs
Route::post( 'loan/apply', [ LoanController::class, 'applyforLoan' ] );
Route::post( 'loan/approve', [ LoanController::class, 'approveLoan' ] );
Route::post( 'loan/emi/pay', [ LoanController::class, 'payLoanEMI' ] );