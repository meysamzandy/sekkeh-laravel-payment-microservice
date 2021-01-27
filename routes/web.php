<?php

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

Route::get('/health1', function () {
    return response("i am alive",200);
});

Route::get('/health2', function () {
    try{
        \Illuminate\Support\Facades\DB::connection()->statement("show tables");
        return response("i am ok",200);
    }catch (Exception $e){
        return response("i am not ok",500);
    }
});
