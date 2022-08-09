<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeagueController;

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

Route::get('/create-user', function (Request $request) {
    $user = \App\Models\User::query()->first();

    if (!$user) {
        $user = new \App\Models\User();
        $user->name = "Test";
        $user->email = "Test@etest";
        $user->password = '';

        $user->save();
    }

    return $user->createToken('some token')->plainTextToken;
});

Route::get('/login', function (Request $request) {

})->name('login');


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('leagues', [LeagueController::class, 'index']);
    Route::get('leagues/{league_id}', [LeagueController::class, 'show'])
        ->where('league_id', '[0-9]+');;
});
