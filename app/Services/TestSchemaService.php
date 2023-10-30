<?php

namespace App\Services;

use Exception;
use TypeError;
use DivisionByZeroError;
use App\Models\OutputCell;
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

    public function getTestSchemaByGroupId($groupId){
        return TestSchema::where('test_schema_group_id', $groupId)->get();
    }

    public function getInputCellValueBySchemaId($schemaId){
        return InputCellValue::where('test_schema_id', $schemaId)->get();
    }

    public function getOutputCellValueBySchemaId($schemaId){
        return OutputCellValue::where('test_schema_id', $schemaId)->get();
    }

    public function generateActualValueSchema($schemaId){
        try{
            $outputCells = OutputCell::whereHas("excel_version", function($excelversion) use ($schemaId){
                $excelversion->whereHas("test_schema_group", function($testSchemaGroup) use ($schemaId){
                    $testSchemaGroup->whereHas("test_schema", function($testSchema) use ($schemaId){
                        $testSchema->where("id", $schemaId);
                    });
                });
            })->get();
    
            $inputValues = InputCellValue::select("input_cell_id", "value")
                ->whereTestSchemaId($schemaId)
                ->get();
    
            $excelversion = $outputCells[0]->excel_version;
            $excel = $this->excelService->getCalculateExcelValue(
                public_path("excel\\{$excelversion->alkes->excel_name}-{$excelversion->version_name}.xlsx"),
                $inputValues,
                "LH"
            );
    
            foreach($outputCells as $outputCell) {
                $actual_value = '';
                $isVerified = true;
                $error_description = '';
                $expected_value = '';
    
                try{
                    $actual_value = $excel->getCell($outputCell->cell)->getFormattedValue();
                    
                    if($actual_value == "#N/A"){
                        $expected_value = '';
                    }else{
                        $expected_value = $actual_value;
                    }
                }catch (DivisionByZeroError $e) {
                    $expected_value = '';
                    $actual_value = "#DIV/0";
                    $isVerified = false;
                    $error_description = "#DIV/0";
                }catch(Exception $e){
                    $expected_value = '';
                    $isVerified = false;
                    $error_description = $e->getMessage();
                }catch(TypeError $e){
                    $expected_value = '';
                    $isVerified = false;
                    $error_description = $e->getMessage(); 
                }
                
                OutputCellValue::create([
                    'output_cell_id' => $outputCell->id,
                    'expected_value' => $expected_value,
                    'actual_value' => $actual_value,
                    'test_schema_id' => $schemaId,
                    'is_verified' => $isVerified,
                    'error_description' => $error_description,
                ]);
            }
        }catch(Exception $e){
            return $this->generateActualValueSchema($schemaId);
        }

        $this->testSimulation($excelversion->id, $schemaId);
        return true;
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
                    $output_value->error_description = "Nilai Aktual Tidak sama Dengan Nilai Ekspektasi";
                }
            }catch (DivisionByZeroError $e) {
                $output_value->actual_value = "#DIV/0";
                $output_value->is_verified = false;
                $output_value->error_description = "#DIV/0";
            }catch(Exception $e){
                $output_value->is_verified = false;
                $output_value->error_description = $e->getMessage();
            }catch(TypeError $e){
                $output_value->is_verified = false;
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
    
            foreach($data as $key => $value){
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

            $this->testSimulation($testSchema->test_schema_group->excel_version_id, $testSchema->id);
            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            return false;
        }
    }

    public function detailschemaSimulation($versionId, $schemaId){
        
    }
}