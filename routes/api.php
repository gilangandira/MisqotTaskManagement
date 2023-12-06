<?php

use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VendorController;

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
/////////////////USer////////////
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('users/me', [AuthController::class, 'me']);
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/users/delete/{id}', [AuthController::class, 'destroy']);

    ////////////////////Assets////////////
    Route::get('/assets', [AssetsController::class, 'index']);
    Route::post('/assets/store', [AssetsController::class, 'store']);
    Route::post('/assets/update/{id}', [AssetsController::class, 'update']);
    Route::delete('/assets/delete/{id}', [AssetsController::class, 'destroy']);
    Route::get('/assets/condition', [AssetsController::class, 'condition']);
    Route::get('/assets/category', [AssetsController::class, 'category']);
    Route::get('/assets/search/', [AssetsController::class, 'search']);

    /////////////////Customer///////////////
    Route::post('/customers/store', [CustomerController::class, 'store']);


    Route::delete('/customers/delete/{id}', [CustomerController::class, 'destroy']);
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/paginate', [CustomerController::class, 'paginate']);
    Route::post('/customers/update/{id}', [CustomerController::class, 'update']);

    ////////////////Vendor//////////////
    Route::get('/vendor', [VendorController::class, 'index']);
    Route::get('/listvendor', [VendorController::class, 'listvendor']);
    Route::post('/vendor/store', [VendorController::class, 'store']);
    Route::put('/vendor/update/{id}', [VendorController::class, 'update']);
    Route::delete('/vendor/delete/{id}', [VendorController::class, 'destroy']);

    //////////////Tasks///////////
    Route::get('/task', [TaskController::class, 'index']);
    Route::get('/task/jobuser/{taskId}', [TaskController::class, 'jobuser']);
    Route::get('/task/sla', [TaskController::class, 'sla']);
    Route::post('/task/store', [TaskController::class, 'store']);
    Route::delete('/task/delete/{id}', [TaskController::class, 'destroy']);
    Route::post('/task/update/{id}', [TaskController::class, 'update']);

    Route::post('/task/do/{id}', [TaskController::class, 'do']);
    Route::post('/task/start/{id}', [TaskController::class, 'start']);
    Route::post('/task/pause/{id}', [TaskController::class, 'pause']);
    Route::post('/task/end/{id}', [TaskController::class, 'end']);
    Route::get('/task/stopwatch/{id}', [TaskController::class, 'stopwatch']);
    Route::get('/performance', [TaskController::class, 'performance']);
});
Route::get('/users', [AuthController::class, 'index']);

Route::get('/images/{id}', [AuthController::class, 'getUserImage']);
Route::post('/users/update/{id}', [AuthController::class, 'update']);

Route::get('/customers/count', [CustomerController::class, 'totalData']);
Route::get('/customers/fee', [CustomerController::class, 'totalBiaya']);
Route::get('/customers/bandwith', [CustomerController::class, 'totalBandwith']);


///////////assets/////////////////


Route::post('/performance/store', [PerformanceController::class, 'store']);
Route::get('/performance', [PerformanceController::class, 'index']);
Route::get('/totalassets', [AssetsController::class, 'totalAssets']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
