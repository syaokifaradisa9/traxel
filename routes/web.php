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
    Route::name('logout')->get('logout', function(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('login');
    });

    Route::controller(HomeController::class)->group(function(){
        Route::prefix('home')->group(function(){
            Route::get('/', 'index')->name('home');
            Route::prefix('{alkes_id}/version')->name('version.')->group(function(){
                Route::get('/', 'excelVersion')->name('index');
                Route::get('create', 'createExcelVersion')->name('create');
                Route::post('store', 'storeExcelVersion')->name('store');
                Route::prefix("{version_id}")->group(function(){
                    Route::get('/edit', 'editExcelVersion')->name('edit');
                    Route::get('/delete', 'deleteExcelVersion')->name('delete');
                    Route::put('/update', 'updateExcelVersion')->name('update');
                    Route::prefix("schema")->name('schema.')->group(function(){
                        Route::get('/', 'trackingSchema')->name('index');
                        Route::get('create', 'createSimulation')->name("create-simulation");
                        Route::post('store', 'storeSimulation')->name("store-simulation");
                        Route::get('simulation', 'allSimulation')->name("all-simulation");
                        Route::prefix("{schema_id}")->group(function(){
                            Route::get('cell-tracker', 'cellTracker')->name('cell-tracker');
                            Route::get('edit', 'editSimulation')->name("edit-simulation");
                            Route::get('duplicate', 'duplicateSimulation')->name("duplicate-simulation");
                            Route::put('update', 'updateSimulation')->name("update-simulation");
                            Route::get('simulation', 'schemaSimulation')->name("simulation");
                            Route::get('detail', 'detailSimulation')->name("detail-simulation");
                            Route::get('detail-simulation', 'detailschemaSimulation')->name("detail-schema-simulation");
                        });
                    });
                    Route::get('{type}', 'editCellNameExcelVersion')->name('set-cell-name');
                    Route::post('{type}/update', 'updateCellNameExcelVersion')->name('update-cell-name');
                });
            });
        });
    });

    Route::name('tutorial')->get('tutorial', function(){
        return view('tutorial.index');
    });
});