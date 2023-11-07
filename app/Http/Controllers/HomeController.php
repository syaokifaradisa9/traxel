<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Alkes;
use App\Models\InputCell;
use App\Models\Calibrator;
use App\Models\OutputCell;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use Illuminate\Http\Request;
use App\Services\AlkesService;
use App\Services\ExcelService;
use App\Models\GroupCalibrator;
use App\Models\OutputCellValue;
use App\Models\TestSchemaGroup;
use App\Services\TestSchemaService;
use App\Services\ExcelVersionService;
use App\Services\GroupSimulationService;
use App\Http\Requests\ImportVersionRequest;
use App\Services\CalibratorService;

class HomeController extends Controller
{
    private $alkesService;
    private $excelVersionService;
    private $testSchemaService;
    private $excelService;
    private $groupSimulationService;
    private $calibratorService;
    public function __construct(
            AlkesService $alkesService, 
            ExcelVersionService $excelVersionService, 
            TestSchemaService $testSchemaService, 
            ExcelService $excelService,
            GroupSimulationService $groupSimulationService,
            CalibratorService $calibratorService,
        ){
            $this->alkesService = $alkesService;
            $this->excelVersionService = $excelVersionService;
            $this->testSchemaService = $testSchemaService;
            $this->excelService = $excelService;
            $this->groupSimulationService = $groupSimulationService;
            $this->calibratorService = $calibratorService;
    }

    /* ============================== Excel Version ============================== */

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
        if($this->excelVersionService->saveExcelVersion($request->all(), $alkesId)){
            return to_route('version.index', ['alkes_id' => $alkesId])->with('success', "Berhasil Menambahkan Versi Excel");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function exportExcelVersion($alkesId, $versionId){
        $this->excelVersionService->exportExcelVersion($alkesId, $versionId);
    }

    public function importExcelVersion(ImportVersionRequest $request, $alkesId){
        if($this->excelVersionService->importExcelVersion($request->file('file_import'))){
            return to_route('version.index', ['alkes_id' => $alkesId])->with('success', "Sukses Mengimport Versi Excel");
        }else{
            return back()->withInput()->with('error', 'Terjadi Kesalahan, Silahkan Coba Lagi!');
        }
    }

    public function editCellNameExcelVersion($alkesId, $versionId, $type){
        $cells = $type == "input" ? InputCell::where('excel_version_id', $versionId)->get() : OutputCell::where('excel_version_id', $versionId)->get();
        return view('excel_version.cells', compact('alkesId', 'versionId', 'type', 'cells'));
    }

    public function updateCellNameExcelVersion(Request $request, $alkesId, $versionId, $type){
        if($this->excelVersionService->updateCellNameByVersionId($request->all(), $versionId, $type)){
            return to_route('version.index', ['alkes_id' => $alkesId])->with('success', "Berhasil Mengubah Nama Cell");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    /* ============================== Calibrator Group ============================== */

    public function calibratorGroupImport(Request $request, $alkesId, $versionId){
        if($this->calibratorService->calibratorImport($request->file('calibrator_file'), $versionId)){
            return to_route('version.calibrator-group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])->with('success', "Sukses Menambahkan Group Calibrator!");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function calibratorGroupIndex($alkesId, $versionId){
        $group_calibrators = GroupCalibrator::where('excel_version_id', $versionId)->orderBy('name')->get();
        return view('calibrator.index', compact('alkesId', 'versionId', 'group_calibrators'));
    }

    public function calibratorGroupStore(Request $request, $alkesId, $versionId){
        if($this->calibratorService->storeCalibratorGroup($request->name, $request->cell_id, $request->cell_lh ?? '', $versionId)){
            return to_route('version.calibrator-group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])->with('success', "Sukses Menambahkan Group Calibrator!");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function calibratorGroupEdit($alkesId, $versionId, $group_id){
        $group_calibrators = GroupCalibrator::where('excel_version_id', $versionId)->orderBy('name')->get();
        $calibrator_group = GroupCalibrator::find($group_id);
        $isEdit = true;
        return view('calibrator.index', compact('alkesId', 'versionId', 'group_calibrators', 'calibrator_group', 'isEdit'));
    }

    public function calibratorGroupUpdate(Request $request, $alkesId, $versionId, $groupId){
        if($this->calibratorService->updateCalibratorGroup($request->name, $request->cell_id, $request->cell_lh ?? '', $versionId, $groupId)){
            return to_route('version.calibrator-group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])->with('success', "Sukses Mengubah Group Calibrator!");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function calibratorGroupExport($alkesId, $versionId, $group_id){
        return $this->calibratorService->exportGroupCalibrator($group_id);
    }

    public function calibratorGroupDelete($alkesId, $versionId, $group_id){
        if($this->calibratorService->delete($group_id)){
            return to_route('version.calibrator-group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])->with('success', "Sukses Menghapus Group Calibrator!");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    /* ============================== Calibrator ============================== */

    public function calibratorIndex($alkesId, $versionId, $groupId){
        $calibrators = Calibrator::where('group_calibrator_id', $groupId)->orderBy('merk')->get();
        return view('calibrator.calibrator', compact('alkesId', 'versionId', 'groupId', 'calibrators'));
    }

    public function calibratorStore(Request $request, $alkesId, $versionId, $groupId){
        if($this->calibratorService->calibratorStore($request->name, $request->merk, $request->model_type, $request->model_type_name, $request->serial_number, $groupId)){
            return to_route('version.calibrator-group.calibrator.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId])->with('success', "Sukses Menambahkan Calibrator!");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function calibratorEdit($alkesId, $versionId, $groupId, $calibratorId){
        $calibrator = Calibrator::find($calibratorId);
        $calibrators = Calibrator::where('group_calibrator_id', $groupId)->orderBy('merk')->get();
        $isEdit = true;
        return view('calibrator.calibrator', compact('alkesId', 'versionId', 'groupId', 'calibrators', 'calibrator', 'isEdit'));
    }

    public function calibratorDuplicate($alkesId, $versionId, $groupId, $calibratorId){
        $calibrator = Calibrator::find($calibratorId);
        $calibrators = Calibrator::where('group_calibrator_id', $groupId)->orderBy('merk')->get();
        return view('calibrator.calibrator', compact('alkesId', 'versionId', 'groupId', 'calibrators', 'calibrator'));
    }

    public function calibratorUpdate(Request $request, $alkesId, $versionId, $groupId, $calibratorId){
        if($this->calibratorService->calibratorUpdate($calibratorId, $groupId, $request->name, $request->merk, $request->model_type, $request->model_type_name, $request->serial_number)){
            return to_route('version.calibrator-group.calibrator.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId])->with('success', "Sukses Mengubah Calibrator!");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function calibratorDelete($alkesId, $versionId, $groupId, $calibratorId){
        try{
            Calibrator::find($calibratorId)->delete();
            return to_route('version.calibrator-group.calibrator.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId])->with('success', "Sukses menghapus Calibrator!");
        }catch(Exception $e){
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    /* ============================== Group Simulasi ============================== */

    public function trackingSchemaGroup($alkesId, $versionId){
        $test_schema_groups = TestSchemaGroup::where('excel_version_id', $versionId)->get();
        return view('schema_group.index', compact('test_schema_groups', 'alkesId', 'versionId'));
    }

    public function createSchemaGroup($alkesId, $versionId){
        $inputCells = InputCell::where('excel_version_id', $versionId)->where(function($q){
            $q->where('cell_name', "!=", "Cell Kalibrator");
            $q->orWhereNUll('cell_name');
        })->get();
        $inputCellValues = collect($this->excelVersionService->getInputCellValueWithRedTextInSheetByExcelversion($versionId));

        return view('schema_group.create', compact('versionId', 'alkesId', 'inputCells', 'inputCellValues'));
    }

    public function storeSimulationGroup(Request $request, $alkesId, $versionId){
        if($this->groupSimulationService->store($request->all(), $versionId)){
            return to_route('version.schema_group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])->with('success', "Sukses Menambahkan Grup Simulasi");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function deleteSimulationGroup($alkesId, $versionId, $groupId){
        if($this->groupSimulationService->delete($groupId)){
            return to_route('version.schema_group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])->with('success', "Sukses Menghapus Grup Simulasi");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    /* ============================== Simulasi ============================== */

    public function generates(Request $request, $alkesId, $versionId, $groupId){
        if($this->groupSimulationService->generatesActualValues($groupId, $request->num)){
            return to_route('version.schema_group.schema.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId])->with('success', "Sukses Menambahkan Grup Simulasi");
        }else{
            return back()->withInput()->with('error', "Terjadi Kesalahan, Silahkan Coba Lagi!");
        }
    }

    public function trackingSchema($alkesId, $versionId, $groupId){
        $schemas = $this->testSchemaService->getTestSchemaByGroupId($groupId);
        return view('schemas.index', compact('schemas', 'alkesId', 'versionId', 'groupId'));
    }

    public function createSimulation($alkesId, $versionId){
        $inputCells = InputCell::where('excel_version_id', $versionId)->get();
        $inputCellValues = collect($this->excelVersionService->getInputCellValueWithRedTextInSheetByExcelversion($versionId));
        $outputCells = OutputCell::where('excel_version_id', $versionId)->get();

        return view('schemas.create', compact('versionId', 'alkesId', 'inputCells', 'outputCells', 'inputCellValues'));
    }

    public function editSimulation($alkesId, $versionId, $groupId, $schemaId){
        $outputCells = OutputCell::where('excel_version_id', $versionId)->where(function($q){
            $q->where('cell_name', "!=", "Cell Kalibrator");
            $q->orWhereNUll('cell_name');
        })->get();

        $outputCellValues = collect(OutputCellValue::where('test_schema_id', $schemaId)->get()->map(function($outputCellValue){
            return [
                "id" => $outputCellValue->id,
                "cell" => $outputCellValue->output_cell->cell,
                "cell_name" => $outputCellValue->output_cell->cell_name,
                "expected_value" => $outputCellValue->expected_value
            ]; 
        }));

        $schema = TestSchema::find($schemaId);

        return view('schemas.create', compact('versionId', 'alkesId', 'outputCells', 'outputCellValues', 'schema', 'groupId'));
    }

    public function updateSimulation(Request $request, $alkesId, $versionId, $groupId, $schemaId){
        if($this->testSchemaService->updateSimulation($request->all(), $schemaId)){
            return to_route('version.schema_group.schema.index', [
                'alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId
            ])->with('success', 'Berhasil Mengubah Skema Simulasi');
        }else{
            return back()->withInput()->with('error', 'Terjadi kesalahan, Silahkan Coba Lagi!');
        }
    }

    public function allSimulation(Request $request, $alkesId, $versionId, $groupId){
        $simulations = TestSchema::where('test_schema_group_id', $groupId)
                            ->orderBy("simulation_date", "ASC")
                            ->limit($request->num)
                            ->get();
                            
        foreach($simulations as $simulation){
            $this->testSchemaService->testSimulation($versionId, $simulation->id);
        }

        return to_route('version.schema_group.schema.index', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId,
            'group_id' => $groupId
        ])->with("success", "Semua Simulasi Telah Dilakukan, Silahkan Lihat Hasil Pada Halaman Detail Skema Simulasi!");
    }

    public function generateActualValues($alkesId, $versionId, $groupId, $schemaId){
        if($this->testSchemaService->generateActualValueSchema($schemaId)){
            return to_route('version.schema_group.schema.index', [
                'alkes_id' => $alkesId,
                'version_id' => $versionId,
                'group_id' => $groupId,
            ])->with("success", "Generate Sukses");
        }

        return to_route('version.schema_group.schema.index', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId,
            'group_id' => $groupId
        ])->with("error", "Generate Gagal");
    }

    public function detailSimulation($alkesId, $versionId, $groupId, $schemaId){
        $input_cell_value = $this->testSchemaService->getInputCellValueBySchemaId($schemaId);
        $output_cell_value = $this->testSchemaService->getOutputCellValueBySchemaId($schemaId);

        return view('schemas.detail', compact('alkesId', 'versionId', 'schemaId', 'input_cell_value', 'output_cell_value', 'groupId'));
    }

    public function schemaSimulation($alkesId, $versionId, $groupId, $schemaId){
        $this->testSchemaService->testSimulation($versionId, $schemaId);
        return to_route('version.schema_group.schema.index', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId,
            'group_id' => $groupId
        ])->with("success", "Simulasi Telah Dilakukan, Silahkan Lihat Hasil Pada Halaman Detail Skema Simulasi!");
    }

    public function detailschemaSimulation($alkesId, $versionId, $schemaId, $groupId){
        $this->testSchemaService->testSimulation($versionId, $schemaId);
        return to_route('version.schema_group.schema.detail-simulation', [
            'alkes_id' => $alkesId,
            'version_id' => $versionId,
            'schema_id' => $schemaId,
            'group_id' => $groupId,
        ])->with("success", "Simulasi Telah Dilakukan!");
    }

    public function cellTracker(Request $request, $alkesId, $versionId, $schemaId, $groupId){
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

        return view('cell_trackers.index', compact('excel_values', 'alkesId', 'versionId', 'schemaId', 'error_result_cells', 'sheet_names', 'selected_sheet', 'groupId'));
    }
}
