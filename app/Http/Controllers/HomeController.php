<?php

namespace App\Http\Controllers;

use App\Models\InputCell;
use App\Models\InputCellValue;
use App\Models\OutputCell;
use App\Models\OutputCellValue;
use App\Models\TestSchema;
use App\Services\AlkesService;
use App\Services\ExcelService;
use App\Services\ExcelVersionService;
use App\Services\TestSchemaService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $alkesService;
    private $excelVersionService;
    private $testSchemaService;
    public function __construct(AlkesService $alkesService, ExcelVersionService $excelVersionService, TestSchemaService $testSchemaService){
        $this->alkesService = $alkesService;
        $this->excelVersionService = $excelVersionService;
        $this->testSchemaService = $testSchemaService;
    }

    public function index(){
        $alkes = $this->alkesService->getAlkes();
        return view('home.index', compact('alkes'));
    }

    public function excelVersion($alkesId){
        $versions = $this->excelVersionService->getVersionByAlkesId($alkesId);
        return view('excel_version.index', compact('versions', 'alkesId'));
    }

    public function trackingSchema($alkesId, $versionId){
        $schemas = $this->testSchemaService->getTestSchemaByVersionId($versionId);
        return view('schemas.index', compact('schemas', 'alkesId', 'versionId'));
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
}
