<?php

namespace App\Services;

use Exception;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use App\Models\InputCellValue;
use App\Models\OutputCellValue;

class TestSchemaService{
    private $excelService;
    public function __construct(ExcelService $excelService){
        $this->excelService = $excelService;
    }

    public function getTestSchemaByVersionId($versionId){
        return TestSchema::where('excel_version_id', $versionId)->get();
    }

    public function getInputCellValueBySchemaId($schemaId){
        return InputCellValue::where('test_schema_id', $schemaId)->get();
    }

    public function getOutputCellValueBySchemaId($schemaId){
        return OutputCellValue::where('test_schema_id', $schemaId)->get();
    }

    public function testSimulation($versionId, $schemaId){
        $input_cell_values = InputCellValue::where('test_schema_id', $schemaId)->get();
        $expected_output_cell_values = OutputCellValue::where('test_schema_id', $schemaId)->get();

        $excelversion = ExcelVersion::find($versionId);
        $excel = $this->excelService->getCalculateExcelValue(
            public_path("excel\\ECG_Recorder-{$excelversion->version_name}.xlsx"),
            $input_cell_values,
            "LH"
        );

        foreach($expected_output_cell_values as $expected_output_value){
            $output_value = OutputCellValue::find($expected_output_value->id);
            try{
                $actual_value = $excel->getCell($output_value->cell)->getFormattedValue();
                $output_value->actual_value = $actual_value;
                if($actual_value == $expected_output_value->expected_value){
                    $output_value->is_verified = true;
                }else{
                    $output_value->is_verified = false;
                    $output_value->error_description = "Nilai Ekspektasi Tidak sama Dengan Nilai Aktual";
                }
            }catch(Exception $e){
                $output_value->verified = false;
                $output_value->error_description = $e->getMessage();
            }
            $output_value->save();
        }

        $TestSchema = TestSchema::find($schemaId);
        $TestSchema->simulation_date = date("Y-m-d", strtotime(now()));
        $TestSchema->simulation_time = date("H:i:s", strtotime(now()));
        $TestSchema->save();

        return true;
    }
}