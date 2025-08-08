<?php

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Auth\SocialAuthController;

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

Route::get('/', function () {
    $id = User::inRandomOrder()->first()->id;
    return view('welcome')->with('id', $id);
});

Route::get('/charge', function () {
    return view('charge');
});

Route::get('/noti-test', function(){
    return view('app');
});

Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);

Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
