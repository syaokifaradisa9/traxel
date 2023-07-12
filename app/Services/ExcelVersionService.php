<?php

namespace App\Services;

use App\Models\Alkes;
use App\Models\InputCell;
use App\Models\OutputCell;
use App\Models\ExcelVersion;
use App\Models\OutputCellValue;
use Exception;
use Illuminate\Support\Facades\DB;

class ExcelVersionService{
    public function getVersionByAlkesId($alkesId){
        return ExcelVersion::with(['input_cell', 'output_cell'])->where('alkes_id', $alkesId)->get();
    }

    public function saveExcelVersion($data, $alkesId){
        try{
            DB::beginTransaction();

            $alkes = Alkes::find($alkesId);

            $file = $data['file'];
            $fileName = $alkes->excel_name . "-" . $data['version_name']. ".xlsx";
            $filePath = public_path("excel");
            $file->move($filePath, $fileName);
    
            $version = ExcelVersion::create([
                'version_name' => $data['version_name'],
                'alkes_id' => $alkesId
            ]);
    
            $input_cells = preg_replace('/\s+/', '', $data['input_cell']);
            $input_cells = explode(",", $input_cells);
            foreach($input_cells as $inputCell){
                InputCell::create([
                    'excel_version_id' => $version->id,
                    'cell' => $inputCell
                ]);
            }
    
            $output_cells = preg_replace('/\s+/', '', $data['output_cell']);
            $output_cells = explode(",", $output_cells);
            foreach($output_cells as $outputCell){
                OutputCell::create([
                    'excel_version_id' => $version->id,
                    'cell' => $outputCell
                ]);
            }

            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
            return false;
        }
    }

    public function updateCellNameByVersionId($data, $versionId, $type){
        try{
            DB::beginTransaction();

            unset($data['_token']);
            foreach($data as $key => $cellName){
                if(str_contains($key, "name")){
                    $idcell = explode("-", $key)[1];
                    $newCell = $data[$idcell];
                    
                    if($type == "input"){
                        $inputCell = InputCell::find($idcell);

                        $inputCell->cell = $newCell;
                        $inputCell->cell_name = $cellName;
                        $inputCell->save();
                    }else{
                        $outputCell = OutputCell::find($idcell);

                        $outputCell->cell = $newCell;
                        $outputCell->cell_name = $cellName;
                        $outputCell->save();
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
}