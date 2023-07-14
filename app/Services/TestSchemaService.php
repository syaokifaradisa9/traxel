<?php

namespace App\Services;

use Exception;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use App\Models\InputCellValue;
use App\Models\OutputCellValue;
use Illuminate\Support\Facades\DB;

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
            public_path("excel\\{$excelversion->alkes->excel_name}-{$excelversion->version_name}.xlsx"),
            $input_cell_values,
            "LH"
        );

        foreach($expected_output_cell_values as $expected_output_value){
            $output_value = OutputCellValue::find($expected_output_value->id);
            try{
                $actual_value = $excel->getCell($output_value->output_cell->cell)->getFormattedValue();
                $output_value->actual_value = $actual_value;
                if($actual_value == $expected_output_value->expected_value){
                    $output_value->is_verified = true;
                    $output_value->error_description = "";
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

    public function saveSimulation($data, $excelVersionId){
        try{
            DB::beginTransaction();

            $testSchema = TestSchema::create([
                'excel_version_id' => $excelVersionId,
                "name" => $data['simulation_name'] ?? 'Simulasi Excel',
            ]);
    
            foreach($data as $key => $value){
                if(str_contains($key, "input")){
                    $input_cell_id = str_replace("input-", "", $key);
                    InputCellValue::create([
                        'input_cell_id' => $input_cell_id,
                        'value' => $value,
                        'test_schema_id' => $testSchema->id,
                    ]);
                }

                if(str_contains($key, "output")){
                    $output_cell_id = str_replace("output-", "", $key);
                    OutputCellValue::create([
                        'output_cell_id' => $output_cell_id,
                        'expected_value' => $value ?? '',
                        'test_schema_id' => $testSchema->id,
                    ]);
                }
            }

            $this->testSimulation($excelVersionId, $testSchema->id);
            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            return false;
        }
    }

    public function updateSimulation($data, $schemaId){
        try{
            DB::beginTransaction();

            $testSchema = TestSchema::find($schemaId);
            $testSchema->name = $data['simulation_name'] ?? 'Simulasi Excel';
            $testSchema->save();
    
            foreach($data as $key => $value){
                if(str_contains($key, "input")){
                    $input_cell_id = str_replace("input-", "", $key);
                    $inputCellValue = InputCellValue::where([
                        'input_cell_id' => $input_cell_id,
                        'test_schema_id' => $testSchema->id,
                    ])->first();
                    $inputCellValue->value = $value ?? '';
                    $inputCellValue->save();
                }

                if(str_contains($key, "output")){
                    $output_cell_id = str_replace("output-", "", $key);
                    $outputCellValue = OutputCellValue::where([
                        'output_cell_id' => $output_cell_id,
                        'test_schema_id' => $testSchema->id
                    ])->first();
                    $outputCellValue->expected_value = $value ?? '';
                    $outputCellValue->save();
                }
            }

            $this->testSimulation($testSchema->excel_version_id, $testSchema->id);
            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            return false;
        }
    }
}