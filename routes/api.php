<?php

use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\ListsController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\AuthApiUsers;
use App\Http\Middleware\AuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

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

Route::get('/api', function () {
    return response()
        ->json([
            'status' => 'success',
            'message' => 'berhasil reload api'
        ], 200);
});



Route::group(['prefix' => 'board', 'middleware' => 'auth.token'], function () {
    // * Board Resources

    Route::post('/', [BoardController::class, 'create']);
    Route::put('/{board_id}', [BoardController::class, 'update']);
    Route::delete('/{board_id}', [BoardController::class, 'delete']);
    Route::get('/', [BoardController::class, 'index']);
    Route::get('/{board_id}', [BoardController::class, 'show']);

    Route::any('{any}', function () {
        return response()->json([
            'status' => 'error',
            'message' => 'method tidak ditemukan'
        ], 404);
    });
});


Route::group(['prefix' => 'board/{board_id}/member', 'middleware' => 'auth.token'], function () {
    // * Member Resources

    Route::post('/', [MemberController::class, 'add']);
    Route::delete('/{user_id}', [MemberController::class, 'delete']);

    Route::any('{any}', function () {
        return response()->json([
            'status' => 'error',
            'message' => 'method tidak ditemukan'
        ], 404);
    });
});

Route::group(['prefix' => 'board/{board_id}/list', 'middleware' => 'auth.token'], function () {
    // * Resources

    Route::post('/', [ListsController::class, 'add']);
    Route::put('/{list_id}', [ListsController::class, 'update']);


    Route::any('{any}', function () {
        return response()->json([
            'status' => 'error',
            'message' => 'method tidak ditemukan'
        ], 404);
    });
});


Route::group(['prefix' => 'auth', 'middleware' => 'auth.session'], function () {
    // * Auth Resources

    Route::post('/login', [AuthApiUsers::class, 'login']);
    Route::post('/register', [AuthApiUsers::class, 'registrasi']);
});

Route::any('{any}', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'method tidak ditemukan'
    ], 404);
});
