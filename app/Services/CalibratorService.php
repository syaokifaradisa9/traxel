<?php

namespace App\Services;

use Exception;
use App\Models\Calibrator;
use App\Models\GroupCalibrator;

class CalibratorService{
    public function storeCalibratorGroup($name, $cell_id, $cell_lh, $versionId){
        try{
            GroupCalibrator::create([
                'name' => $name,
                'cell_ID' => $cell_id,
                'cell_LH' => $cell_lh,
                'excel_version_id' => $versionId
            ]);

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function updateCalibratorGroup($name, $cell_id, $cell_lh, $versionId, $groupId){
        try{
            GroupCalibrator::find($groupId)->update([
                'name' => $name,
                'cell_ID' => $cell_id,
                'cell_LH' => $cell_lh,
                'excel_version_id' => $versionId
            ]);

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function calibratorStore($name, $merk, $model_type, $model_type_name, $serial_number, $groupId){
        try{
            Calibrator::create([
                'name' => $name,
                'merk' => $merk,
                'model_type' => $model_type,
                'model_type_name' => $model_type_name,
                'serial_number' => $serial_number,
                'group_calibrator_id' => $groupId
            ]);

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function calibratorUpdate($calibratorId, $groupId, $name, $merk, $model_type, $model_type_name, $serial_number){
        try{
            Calibrator::find($calibratorId)->update([
                'name' => $name,
                'merk' => $merk,
                'model_type' => $model_type,
                'model_type_name' => $model_type_name,
                'serial_number' => $serial_number,
                'group_calibrator_id' => $groupId
            ]);

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function calibratorImport($fileJson, $versionId){
        try{
            $fileJson->move(
                public_path("temp"), 
                "calibrator.json"
            );
    
            $fileContents = file_get_contents(public_path("temp/calibrator.json"));
            $data = json_decode($fileContents, true);
    
            $groupcalibrator = GroupCalibrator::create([
                'name' => $data['name'],
                'cell_ID' => '',
                'cell_LH' => '',
                'excel_version_id' => $versionId
            ]);
    
            foreach($data['calibrator'] as $calibrator){
                Calibrator::create([
                    'name' => $calibrator['name'],
                    'merk' => $calibrator['merk'],
                    'model_type' => $calibrator['model_type'],
                    'model_type_name' => $calibrator['model_type_name'],
                    'serial_number' => $calibrator['serial_number'],
                    'group_calibrator_id' => $groupcalibrator->id
                ]);
            }

            return true;
        }catch(Exception $e){
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