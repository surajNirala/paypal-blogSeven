<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

// payment create
Route::post('create',"PaymentController@create")->name("create-payment");

// payment execute
Route::get("/execute-payment","PaymentController@execute")->name("execute-payment");

//fetch sales
Route::get("sale/{salesId}","PaymentController@getSales")->name("getSales");

//fetch payment history
Route::get("paymentList","PaymentController@getPaymentList");

//fetch payment Details
Route::get("paymentDetails/{payID}","PaymentController@getpaymentDetails");