<?php

namespace App\Http\Controllers;

use App\Models\Alkes;
use App\Models\InputCell;
use App\Models\OutputCell;
use App\Models\TestSchema;
use Illuminate\Http\Request;
use App\Models\InputCellValue;
use App\Services\AlkesService;
use App\Models\OutputCellValue;
use App\Services\ExcelService;
use App\Services\TestSchemaService;
use App\Services\ExcelVersionService;

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

    public function cellTracker($alkesId, $versionId, $schemaId){
        $excel_values = $this->excelService->getExcelCellValue($versionId, $schemaId);
        $error_result_cells = $this->excelService->getErrorCellInResultSheet($schemaId);

        return view('cell_trackers.index', compact('excel_values', 'alkesId', 'versionId', 'schemaId', 'error_result_cells'));
    }
}
