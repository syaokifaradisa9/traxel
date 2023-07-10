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
        Route::prefix('home')->group(function(){
            Route::get('/', 'index')->name('home');
            Route::prefix('{alkes_id}/version')->name('version.')->group(function(){
                Route::get('/', 'excelVersion')->name('index');
                Route::prefix("{version_id}/schema")->name('schema.')->group(function(){
                    Route::get('/', 'trackingSchema')->name('index');
                    Route::prefix("{schema_id}")->group(function(){
                        Route::get('simulation', 'schemaSimulation')->name("simulation");
                        Route::get('detail', 'detailSimulation')->name("detail-simulation");
                    });
                });
            });
        });
    });
});