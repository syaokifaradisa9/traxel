<?php

namespace App\Services;

use Exception;
use App\Models\InputCell;
use App\Models\Calibrator;
use App\Models\OutputCell;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use App\Models\InputCellValue;
use App\Models\GroupCalibrator;
use App\Models\OutputCellValue;
use App\Models\TestSchemaGroup;
use Illuminate\Support\Facades\DB;

class GroupSimulationService{
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
        try{
            DB::beginTransaction();

            // Membuat Group Skema Simulasi
            $testSchemaGroup = TestSchemaGroup::create([
                "name" => $data['simulation_name'],
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
            $this->generateCombinations($group_calibrators, [], $allCombinations);

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
    
                    if(str_contains($key, "output")){
                        $output_cell_id = str_replace("output-", "", $key);
                        OutputCellValue::create([
                            'output_cell_id' => $output_cell_id,
                            'expected_value' => $value ?? '',
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
            return false;
        }
    }

    public function exportGroupCalibrator($group_id){
        $groupCalibrator = GroupCalibrator::with('calibrator')->find($group_id);
        
        $calibrators = [];
        foreach($groupCalibrator->calibrator as $calibrator){
            $calibrators[] = [
                "name" => $calibrator->name,
                "merk" => $calibrator->merk,
                "model_type" => $calibrator->model_type,
                "model_type_name" => $calibrator->model_type_name,
                "serial_number" => $calibrator->serial_number
            ];
        }

        $jsonData =  json_encode([
            "name" => $groupCalibrator->name,
            "calibrator" => $calibrators
        ]);

        $filename = $groupCalibrator->name . '.json';
        header('Content-Type: application/json');
        header("Content-Disposition: attachment; filename=$filename");

        echo $jsonData;
        exit;
    }

    public function delete($groupId){
        try{
            Calibrator::where("group_calibrator_id", $groupId)->delete();
            GroupCalibrator::find($groupId)->delete();

            return true;
        }catch(Exception $e){
            return false;
        }
    }
}