<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Alkes;
use App\Models\InputCell;
use App\Models\OutputCell;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use Illuminate\Http\Request;
use App\Models\InputCellValue;
use App\Services\AlkesService;
use App\Services\ExcelService;
use App\Models\OutputCellValue;
use Illuminate\Support\Facades\DB;
use App\Services\TestSchemaService;
use App\Services\ExcelVersionService;
use App\Http\Requests\ImportVersionRequest;

class HomeController extends Controller
{
    private $alkesService;
    private $excelVersionService;
    private $testSchemaService;
    private $excelService;
    public function __construct(AlkesService $alkesService, ExcelVersionService $excelVersionService, TestSchemaService $testSchemaService, ExcelService $excelService){
        $this->alkesService = $alkesService;
        $this->excelVersionService = $excelVersionService;
        $this->testSchemaService = $testSchemaService;
        $this->excelService = $excelService;
    }

    public function index(){
        $alkes = $this->alkesService->getAlkes();
        return view('home.index', compact('alkes'));
    }

    public function excelVersion($alkesId){
        $versions = $this->excelVersionService->getVersionByAlkesId($alkesId);
        $excel_name = Alkes::find($alkesId)->excel_name;
        return view('excel_version.index', compact('versions', 'alkesId', 'excel_name'));
    }

    public function createExcelVersion($alkesId){
        return view('excel_version.create', compact('alkesId'));
    }

    public function storeExcelVersion(Request $request, $alkesId){
        $is_success =  $this->excelVersionService->saveExcelVersion($request->all(), $alkesId);
        if($is_success){
            return to_route('version.index', ['alkes_id' => $alkesId])->with('success', "Berhasil Menambahkan Versi Excel");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function editExcelVersion($alkesId, $versionId){
        $version = ExcelVersion::with('input_cell', 'output_cell')->find($versionId);

        $cells = $this->excelVersionService->getInputAndOutputCells($versionId);
        $input_cells = $cells['input'];
        $output_cells = $cells['output'];

        return view('excel_version.create', compact('alkesId', 'version', 'input_cells', 'output_cells'));
    }

    public function updateExcelVersion(Request $request, $alkesId, $versionId){
        $is_success = $this->excelVersionService->updateExcelVersion($request->all(), $alkesId, $versionId);
        if($is_success){
            return to_route('version.index', ['alkes_id' => $alkesId])->with('success', "Berhasil Menambahkan Versi Excel");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function deleteExcelVersion($alkesId, $versionId){
        $this->excelVersionService->deleteExcelVersion($versionId);
        return to_route('version.index', ['alkes_id' => $alkesId]);
    }

    public function exportExcelVersion($alkesId, $versionId){
        $version      = ExcelVersion::select('alkes_id', 'version_name')->find($versionId)->toArray();
        $input_cells  = InputCell::select('cell', 'cell_name')->where('excel_version_id', $versionId)->get();
        $output_cells = OutputCell::select('cell', 'cell_name')->where('excel_version_id', $versionId)->get();
        
        $test_schemas = [];
        $schemes = TestSchema::where('excel_version_id', $versionId)->get();
        foreach ($schemes as $scheme){
            $schema = [
                'name' => $scheme->name
            ];
            
            $inputCellValues = [];
            $input_cell_values = InputCellValue::with('input_cell')->where('test_schema_id', $scheme->id)->get();
            
            foreach($input_cell_values as $cell_value){
                $inputCellValues[] = [
                    "cell" => $cell_value->input_cell->cell,
                    "value" => $cell_value->value
                ];
            }

            $schema['input_cell_values'] = $inputCellValues;

            $outputCellValues = [];
            $output_cell_value = OutputCellValue::with('output_cell')->where('test_schema_id', $scheme->id)->get();
            
            foreach($output_cell_value as $cell_value){
                $outputCellValues[] = [
                    "cell" => $cell_value->output_cell->cell,
                    "value" => $cell_value->value
                ];
            }

            $schema['output_cell_value'] = $outputCellValues;

            $test_schemas[] = $schema;
        }

        $version['cell_input'] = $input_cells;
        $version['cell_output'] = $output_cells;
        $version['schema'] = $test_schemas;
        
        $jsonData = json_encode($version);

        $alkes = Alkes::find($alkesId);
        $filename = $alkes->name . " Versi " . $version['version_name'] . '.json';
        header('Content-Type: application/json');
        header("Content-Disposition: attachment; filename=$filename");

        echo $jsonData;
        exit;
    }

    public function importExcelVersion(ImportVersionRequest $request, $alkesId){
        try{
            DB::beginTransaction();

            $request->file('json')->move(
                public_path("temp"), 
                "version.json"
            );
    
            $fileContents = file_get_contents(public_path("temp/version.json"));
            $data = json_decode($fileContents, true);

            // Menyimpan Versi
            $alkes_id = $data['alkes_id'];

            $alkes = Alkes::find($alkes_id);
            $version_name = $data['version_name'];
    
            $fileName = $alkes->excel_name . "-" . $version_name. ".xlsx";
            $filePath = public_path("excel");

            $request->file('excel')->move($filePath, $fileName);

            $excel_version = ExcelVersion::create([
                'alkes_id' => $alkes_id,
                'version_name' => $version_name
            ]);
    
            // Menyimpan Cell Input
            $cell_inputs = $data['cell_input'];
            foreach($cell_inputs as $cell_input){
                InputCell::create([
                    'cell' => $cell_input['cell'],
                    'cell_name' => $cell_input['cell_name'],
                    'excel_version_id' => $excel_version->id
                ]);
            }
         
            // Menyimpan Cell Output
            $cell_outputs = $data['cell_output'];
            foreach($cell_outputs as $cell_output){
                OutputCell::create([
                    'cell' => $cell_output['cell'],
                    'excel_version_id' => $excel_version->id
                ]);
            }
    
            // Menyimpan Skema
            $schemas = $data['schema'];
            foreach($schemas as $schema){
                $test_schema = TestSchema::create([
                    'name' => $schema['name'],
                    'excel_version_id' => $excel_version->id
                ]);
    
                foreach($schema['input_cell_values'] as $input_cell){
                    $cell_id = InputCell::where('excel_version_id', $excel_version->id)
                                        ->where('cell', $input_cell['cell'])
                                        ->first()->id;
    
                    InputCellValue::create([
                        'input_cell_id' => $cell_id,
                        'value' => $input_cell['value'],
                        'test_schema_id' => $test_schema->id
                    ]);
                }
    
                foreach($schema['output_cell_value'] as $input_cell){
                    $cell_id = OutputCell::where('excel_version_id', $excel_version->id)
                                        ->where('cell', $input_cell['cell'])
                                        ->first()->id;
                    
                    OutputCellValue::create([
                        'output_cell_id' => $cell_id,
                        'expected_value' => $input_cell['value'] ?? '',
                        'actual_value' => '',
                        'test_schema_id' => $test_schema->id,
                        'is_verified' => false,
                        'error_description' => '',
                    ]);
                }
            }

            DB::commit();
            return to_route('version.index', ['alkes_id' => $alkes_id])->with('success', "Sukses Mengimport Versi Excel");
        }catch(Exception $e){
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi Kesalahan, Silahkan Coba Lagi!');
        }
    }

    public function trackingSchema($alkesId, $versionId){
        $schemas = $this->testSchemaService->getTestSchemaByVersionId($versionId);
        return view('schemas.index', compact('schemas', 'alkesId', 'versionId'));
    }

    public function editCellNameExcelVersion($alkesId, $versionId, $type){
        $cells = $type == "input" ? InputCell::where('excel_version_id', $versionId)->get() : OutputCell::where('excel_version_id', $versionId)->get();
        return view('excel_version.cells', compact('alkesId', 'versionId', 'type', 'cells'));
    }

    public function updateCellNameExcelVersion(Request $request, $alkesId, $versionId, $type){
        $is_success = $this->excelVersionService->updateCellNameByVersionId($request->all(), $versionId, $type);
        if($is_success){
            return to_route('version.index', ['alkes_id' => $alkesId])->with('success', "Berhasil Mengubah Nama Cell");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function createSimulation($alkesId, $versionId){
        $inputCells = InputCell::where('excel_version_id', $versionId)->get();
        $outputCells = OutputCell::where('excel_version_id', $versionId)->get();
        return view('schemas.create', compact('versionId', 'alkesId', 'inputCells', 'outputCells'));
    }

    public function duplicateSimulation($alkesId, $versionId, $schemaId){
        $inputCells = InputCell::where('excel_version_id', $versionId)->get();
        $outputCells = OutputCell::where('excel_version_id', $versionId)->get();
        $inputCellValues = collect(InputCellValue::where('test_schema_id', $schemaId)->get());
        $outputCellValues = collect(OutputCellValue::where('test_schema_id', $schemaId)->get());
        $schema = TestSchema::find($schemaId);

        return view('schemas.create', compact('versionId', 'alkesId', 'inputCells', 'outputCells', 'inputCellValues', 'outputCellValues', 'schema'));
    }

    public function storeSimulation(Request $request, $alkesId, $versionId){
        $is_success = $this->testSchemaService->saveSimulation($request->all(), $versionId);
        if($is_success){
            return to_route('version.schema.index', [
                'alkes_id' => $alkesId, 'version_id' => $versionId
            ])->with('success', 'Berhasil Menambahkan Skema Simulasi');
        }else{
            return back()->withInput()->with('error', 'Terjadi kesalahan, Silahkan Coba Lagi!');
        }
    }

    public function editSimulation($alkesId, $versionId, $schemaId){
        $inputCells = InputCell::where('excel_version_id', $versionId)->get();
        $outputCells = OutputCell::where('excel_version_id', $versionId)->get();
        $inputCellValues = collect(InputCellValue::where('test_schema_id', $schemaId)->get());
        $outputCellValues = collect(OutputCellValue::where('test_schema_id', $schemaId)->get());
        $schema = TestSchema::find($schemaId);

        return view('schemas.create', compact('versionId', 'alkesId', 'inputCells', 'outputCells', 'inputCellValues', 'outputCellValues', 'schema'));
    }

    public function updateSimulation(Request $request, $alkesId, $versionId, $schemaId){
        $is_success = $this->testSchemaService->updateSimulation($request->all(), $schemaId);
        if($is_success){
            return to_route('version.schema.index', [
                'alkes_id' => $alkesId, 'version_id' => $versionId
            ])->with('success', 'Berhasil Mengubah Skema Simulasi');
        }else{
            return back()->withInput()->with('error', 'Terjadi kesalahan, Silahkan Coba Lagi!');
        }
    }

    public function allSimulation($alkesId, $versionId){
        $simulations = TestSchema::where('excel_version_id', $versionId)->get();
        foreach($simulations as $simulation){
            $this->testSchemaService->testSimulation($versionId, $simulation->id);
        }

        return to_route('version.schema.index', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId
        ])->with("success", "Semua Simulasi Telah Dilakukan, Silahkan Lihat Hasil Pada Halaman Detail Skema Simulasi!");
    }

    public function detailSimulation($alkesId, $versionId, $schemaId){
        $input_cell_value = $this->testSchemaService->getInputCellValueBySchemaId($schemaId);
        $output_cell_value = $this->testSchemaService->getOutputCellValueBySchemaId($schemaId);

        return view('schemas.detail', compact('alkesId', 'versionId', 'schemaId', 'input_cell_value', 'output_cell_value'));
    }

    public function schemaSimulation($alkesId, $versionId, $schemaId){
        $this->testSchemaService->testSimulation($versionId, $schemaId);
        return to_route('version.schema.index', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId
        ])->with("success", "Simulasi Telah Dilakukan, Silahkan Lihat Hasil Pada Halaman Detail Skema Simulasi!");
    }

    public function detailschemaSimulation($alkesId, $versionId, $schemaId){
        $this->testSchemaService->testSimulation($versionId, $schemaId);
        return to_route('version.schema.detail-simulation', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId,
            'schema_id' => $schemaId
        ])->with("success", "Simulasi Telah Dilakukan!");
    }

    public function cellTracker(Request $request, $alkesId, $versionId, $schemaId){
        $selected_sheet = [];
        if($request->all()){
            $data = $request->except('_token');
            foreach($data as $sheet_name => $_){
                $selected_sheet[] = str_replace("_", " ", $sheet_name);
            }
        }

        $sheet_names = $this->excelService->getAllSheetNames($versionId);
        $excel_values = $this->excelService->getExcelCellValue($versionId, $schemaId, $selected_sheet);
        $error_result_cells = $this->excelService->getErrorCellInResultSheet($schemaId);

        return view('cell_trackers.index', compact('excel_values', 'alkesId', 'versionId', 'schemaId', 'error_result_cells', 'sheet_names', 'selected_sheet'));
    }
}
