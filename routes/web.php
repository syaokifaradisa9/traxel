<?php

use App\Http\Controllers\HomeController;
use App\Models\Calibrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

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
            Route::prefix("excel")->name('excel.')->group(function(){
                Route::get('create', 'excelCreate')->name('create');
                Route::post('store', 'excelStore')->name('store');
            });

            Route::prefix('{alkes_id}/version')->name('version.')->group(function(){
                Route::get('/', 'excelVersion')->name('index');
                Route::get('create', 'createExcelVersion')->name('create');
                Route::post('import', 'importExcelVersion')->name('import');
                Route::post('store', 'storeExcelVersion')->name('store');
                Route::prefix("{version_id}")->group(function(){
                    Route::get('/edit', 'editExcelVersion')->name('edit');
                    Route::get('/delete', 'deleteExcelVersion')->name('delete');
                    Route::put('/update', 'updateExcelVersion')->name('update');
                    Route::get('/export', 'exportExcelVersion')->name('export');
                    Route::prefix('calibrator-group')->name('calibrator-group.')->group(function(){
                        Route::get('/', 'calibratorGroupIndex')->name('index');
                        Route::post('/', 'calibratorGroupStore')->name('store');
                        Route::post('import', 'calibratorGroupImport')->name('import');
                        Route::prefix("{group_id}")->group(function(){
                            Route::get('/', 'calibratorGroupEdit')->name('edit');
                            Route::put('/', 'calibratorGroupUpdate')->name('update');
                            Route::get('export', 'calibratorGroupExport')->name('export');
                            Route::get('delete', 'calibratorGroupDelete')->name('delete');
                            Route::prefix("calibrator")->name('calibrator.')->group(function(){
                                Route::get('/', 'calibratorIndex')->name('index');
                                Route::post('/', 'calibratorStore')->name('store');
                                Route::prefix("{id}")->group(function(){
                                    Route::get('duplicate', 'calibratorDuplicate')->name('duplicate');
                                    Route::get('edit', 'calibratorEdit')->name('edit');
                                    Route::put('update', 'calibratorUpdate')->name('update');
                                    Route::get('delete', 'calibratorDelete')->name('delete');
                                });
                            });
                        });
                    });
                    Route::prefix('schema_group')->name('schema_group.')->group(function(){
                        Route::get('/', 'trackingSchemaGroup')->name("index");
                        Route::get('create', 'createSchemaGroup')->name('create-schemagroup');
                        Route::post('store', 'storeSimulationGroup')->name("store-schemagroup");
                        Route::prefix("{group_id}")->group(function(){
                            Route::get('delete', "deleteSimulationGroup")->name("delete-schemagroup");
                            Route::prefix("schema")->name('schema.')->group(function(){
                                Route::get('/', 'trackingSchema')->name('index');
                                Route::get('create', 'createSimulation')->name("create-simulation");
                                Route::post('store', 'storeSimulation')->name("store-simulation");
                                Route::get('simulation', 'allSimulation')->name("all-simulation");
                                Route::get('generate', 'generates')->name("generates");
                                Route::prefix("{schema_id}")->group(function(){
                                    Route::get('generate', 'generateActualValues')->name("generate-actual-value");
                                    Route::get('cell-tracker', 'cellTracker')->name('cell-tracker');
                                    Route::get('edit', 'editSimulation')->name("edit-simulation");
                                    Route::get('duplicate', 'duplicateSimulation')->name("duplicate-simulation");
                                    Route::put('update', 'updateSimulation')->name("update-simulation");
                                    Route::get('simulation', 'schemaSimulation')->name("simulation");
                                    Route::get('detail', 'detailSimulation')->name("detail-simulation");
                                    Route::get('detail-simulation', 'detailschemaSimulation')->name("detail-schema-simulation");
                                });
                            });
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

    Route::get("report", function(){
        $calibrators = Calibrator::all();

        $alkesCalibrator = [];
        foreach ($calibrators as $calibrator){
            $excelVersion = $calibrator->group_calibrator->excel_version;
            $version = explode("-", $excelVersion->version_name)[0];
            $alkesCalibrator[] = [
                "calibrator" => $calibrator->full_name,
                "alkes" => $excelVersion->alkes->name . ((strlen($version) == 2) ? "" : " " . $version)
            ];
        }

        $alkesCalibrators = [];
        $alkesCalibrator = collect($alkesCalibrator)->groupBy('calibrator');
        foreach($alkesCalibrator as $calibrator => $calibrators){
            $alkes = [];
            foreach($calibrators as $value){
                $alkes[] = $value['alkes'];
            }

            $alkesCalibrators[$calibrator] = $alkes;
        }

        ksort($alkesCalibrators);

        $pdf = Pdf::loadView('calibrator_report', [
            "calibrators" => $alkesCalibrators
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Kalibrator.pdf');
    });
});
