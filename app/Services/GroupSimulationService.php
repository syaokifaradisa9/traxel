<?php

namespace App\Services;

use Exception;
use App\Models\InputCell;
use App\Models\Calibrator;
use App\Models\OutputCell;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use App\Models\InputCellValue;
use App\Services\ExcelService;
use App\Models\GroupCalibrator;
use App\Models\OutputCellValue;
use App\Models\TestSchemaGroup;
use Illuminate\Support\Facades\DB;

class GroupSimulationService{
    private $testSchemaService;
    public function __construct(ExcelService $excelService, TestSchemaService $testSchemaService){
        $this->excelService = $excelService;
        $this->testSchemaService = $testSchemaService;
    }

    private function generateCombinations($group_calibrator, $currentCombination, &$allCombinations) {
        if (count($group_calibrator) == 0) {
            $allCombinations[] = $currentCombination;
            return;
        }
    
        $currentCalibrator = array_shift($group_calibrator);
        
        foreach ($currentCalibrator as $calibrator) {
            $newCombination = $currentCombination;
            $newCombination[] = $calibrator;
            $this->generateCombinations($group_calibrator, $newCombination, $allCombinations);
        }
    }
    
    public function store($data, $versionId){
        DB::beginTransaction();
        try{
            // Membuat Group Skema Simulasi
            $testSchemaGroup = TestSchemaGroup::create([
                "name" => $data['type'] . " - " .$data['simulation_name'],
                'excel_version_id' => $versionId
            ]);

            // Mengelompokkan Kalibrator dan Mendapatkan Cell untuk ID dan LH Kalibrator
            $version = ExcelVersion::find($versionId);
            $group_calibrators = [];
            $id_cell_calibrator = [];
            $lh_cell_calibrator = [];
            foreach($version->group_calibrator as $group_calibrator){
                $calibrators = [];
                if(!in_array($group_calibrator->cell_ID, $id_cell_calibrator)){
                    $id_cell_calibrator[] = $group_calibrator->cell_ID;
                }
                
                if(!in_array($group_calibrator->cell_LH, $lh_cell_calibrator) && $group_calibrator->cell_LH){
                    $lh_cell_calibrator[] = $group_calibrator->cell_LH;
                }
                
                foreach($group_calibrator->calibrator->pluck('full_name')->toArray() as $calibrator){
                    $calibrators[] = [
                        "ID" => $group_calibrator->cell_ID,
                        "LH" => $group_calibrator->cell_LH,
                        'calibrator' => $calibrator
                    ];
                }
                $group_calibrators[] = $calibrators;
            }

            // Membuat Cell Input Kalibrator
            $replace_id_cells = [];
            foreach($id_cell_calibrator as $cell_calibrator){
                $replace_id_cells[] = $cell_calibrator;
                if(!InputCell::where('cell', $cell_calibrator)->where('cell_name', 'Cell Kalibrator')->count()){
                    InputCell::create([
                        'cell' => $cell_calibrator,
                        'cell_name' => 'Cell Kalibrator',
                        'excel_version_id' => $versionId
                    ]);
                }
            }
            $id_cell_calibrator = $replace_id_cells;

            // Membuat Cell Output Kalibrator
            $replace_lh_cells = [];
            foreach($lh_cell_calibrator as $cell_calibrator){
                $replace_lh_cells[] = $cell_calibrator;
                if(!OutputCell::where('cell', $cell_calibrator)->where('cell_name', 'Cell Kalibrator')->count()){
                    OutputCell::create([
                        'cell' => $cell_calibrator,
                        'cell_name' => 'Cell Kalibrator',
                        'excel_version_id' => $versionId
                    ]);
                }

            }
            $lh_cell_calibrator = $replace_lh_cells;

            // Mendapatkan Kombinasi Kalibrator
            $allCombinations = [];
            if($data['type'] == "Full"){
                $this->generateCombinations($group_calibrators, [], $allCombinations);
            }elseif($data['type'] == "Sample"){
                for($i = 0; $i < count($group_calibrators); $i++){
                    foreach($group_calibrators[$i] as $calibrator){
                        $combination = [];
                        $combination[] = $calibrator;

                        for($j = 0; $j < count($group_calibrators); $j++){
                            if($j != $i){
                                $combination[] = $group_calibrators[$j][0];
                            }
                        }

                        $allCombinations[] = $combination;
                    }
                }
                
            }

            // Memasukkan Data Simulasi Per Kombinasi Kalibrator
            foreach($allCombinations as $combination) {

                // Membuat Nama Simulasi Sesuai Kombinasi Kalibrator
                $simulation_name = "";
                foreach($combination as $calibrator){
                    $simulation_name = $simulation_name . "|" . $calibrator['calibrator'];
                }

                // Membuat Skema Simulasi Per Kombinasi
                $testSchema = TestSchema::create([
                    'test_schema_group_id' => $testSchemaGroup->id,
                    "name" => $simulation_name,
                ]);
        
                // Memasukkan Nilai Input dan Output Per Simulasi
                foreach($data as $key => $value){
                    if(str_contains($key, "input")){
                        $input_cell_id = str_replace("input-", "", $key);
                        InputCellValue::create([
                            'input_cell_id' => $input_cell_id,
                            'value' => $value,
                            'test_schema_id' => $testSchema->id,
                        ]);
                    }
                }

                // Memasukkan Input Kalibrator dan Output Ekspektasi Kalibrator
                foreach($combination as $calibrator){
                    $cell_id_id = InputCell::where('cell', $calibrator['ID'])->where('excel_version_id', $versionId)->first()->id;
                    
                    InputCellValue::create([
                        'input_cell_id' => $cell_id_id,
                        'value' => $calibrator['calibrator'],
                        'test_schema_id' => $testSchema->id,
                    ]);
                    
                    if($calibrator['LH']){
                        $cell_lh_id = OutputCell::where('cell', $calibrator['LH'])->where('excel_version_id', $versionId)->first()->id;
                        OutputCellValue::create([
                            'output_cell_id' => $cell_lh_id,
                            'expected_value' => $calibrator['calibrator'],
                            'actual_value' => '',
                            'test_schema_id' => $testSchema->id,
                            'is_verified' => false,
                            'error_description' => '',
                        ]);
                    }
                }
            }

            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
            return false;
        }
    }

    public function generatesActualValues($groupId, $num){
        $schemas = TestSchema::select("id")->whereTestSchemaGroupId($groupId)->where(function($testSchema){
            $testSchema->whereNull("simulation_date");
        })->limit($num)->get();

        foreach($schemas as $schema){
            $this->testSchemaService->generateActualValueSchema($schema->id);
        }

        return true;
    }

    public function delete($groupId){
        DB::beginTransaction();
        try{
            InputCellValue::whereHas("test_schema", function($testSchema) use ($groupId){
                $testSchema->where("test_schema_group_id", $groupId);
            })->delete();
    
            OutputCellValue::whereHas("test_schema", function($testSchema) use ($groupId){
                $testSchema->where("test_schema_group_id", $groupId);
            })->delete();
    
            TestSchema::whereTestSchemaGroupId($groupId)->delete();
            TestSchemaGroup::find($groupId)->delete();

            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            return false;
        }
    }
}
