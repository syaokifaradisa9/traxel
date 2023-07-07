<?php

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function(){
    Route::get('/', function () {
        return view('welcome');
    })->name("login");

    Route::post('verify', function(Request $request){
        if(Auth::attempt($request->only('email', 'password'))){
            return to_route('home');
        }

        return to_route('login')->with("error", "Email atau Password Salah!");
    })->name('verify');
});

Route::middleware('auth')->group(function(){
    Route::controller(HomeController::class)->group(function(){
        Route::get('home', 'index')->name('home');
    });
});