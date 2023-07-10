<?php

namespace App\Http\Controllers;

use App\Services\AlkesService;
use App\Services\ExcelService;
use App\Services\ExcelVersionService;
use App\Services\TestSchemaService;

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
