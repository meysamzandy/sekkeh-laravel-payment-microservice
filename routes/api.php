<?php

use App\Http\Controllers\MellatCallBack;
use App\Http\Controllers\SamanCallBack;
use App\Http\Controllers\TransactionLogController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('payment/request', [TransactionLogController::class, 'paymentRequest']);

Route::any('payment/mellat/callback', [MellatCallBack::class,'callBack'])->name('mellat_callback');
Route::any('payment/saman/callback', [SamanCallBack::class,'callBack'])->name('saman_callback');


Route::group(['prefix' => '/admin/sekkeh/',  'middleware' => ['adminToken']], function()
{
    Route::get('list/transactions', [TransactionLogController::class,'list'] );

});
