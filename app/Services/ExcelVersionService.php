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
            return false;
        }
    }

    public function getInputAndOutputCells($versionId){
        $version = ExcelVersion::with('input_cell', 'output_cell')->find($versionId);

        $input_cells = "";
        $output_cells = "";

        # Mengambil cell input
        foreach($version->input_cell as $index => $cell){
            if($index == 0){
                $input_cells = $cell->cell;
            }else{
                $input_cells = $input_cells.", " . $cell->cell;
            }
        }

        # Mengambil cell output
        foreach($version->output_cell as $index => $cell){
            if($index == 0){
                $output_cells = $cell->cell;
            }else{
                $output_cells = $output_cells.", " . $cell->cell;
            }
        }

        return [
            'input' => $input_cells,
            'output' => $output_cells
        ];
    }

    public function updateExcelVersion($data, $alkesId, $versionId){
        try{
            DB::beginTransaction();

            if(isset($data['file'])){
                $alkes = Alkes::find($alkesId);

                $file = $data['file'];
                $fileName = $alkes->excel_name . "-" . $data['version_name']. ".xlsx";
                $filePath = public_path("excel");
                $file->move($filePath, $fileName);
            }
    
            $version = ExcelVersion::find($versionId);

            $cells = $this->getInputAndOutputCells($versionId);

            $previous_input_cells = explode(", ", $cells['input']);
            $previous_output_cells = explode(", ", $cells['output']);

            // Update Input Cell
            $input_cells = preg_replace('/\s+/', '', $data['input_cell']);
            $input_cells = explode(",", $input_cells);
            $input_cells = array_unique($input_cells);

            foreach($input_cells as $inputCell){
                if(!in_array($inputCell, $previous_input_cells)){
                    InputCell::create([
                        'excel_version_id' => $version->id,
                        'cell' => $inputCell
                    ]);
                }
            }

            foreach($previous_input_cells as $inputCell){
                if(!in_array($inputCell, $input_cells)){
                    InputCell::where([
                        'excel_version_id' => $version->id,
                        'cell' => $inputCell
                    ])->delete();
                }
            }
    
            // Update Output Cell
            $output_cells = preg_replace('/\s+/', '', $data['output_cell']);
            $output_cells = explode(",", $output_cells);
            $output_cells = array_unique($output_cells);

            foreach($output_cells as $outputCell){
                if(!in_array($outputCell, $previous_output_cells)){
                    OutputCell::create([
                        'excel_version_id' => $version->id,
                        'cell' => $inputCell
                    ]);
                }
            }

            foreach($previous_output_cells as $outputCell){
                if(!in_array($outputCell, $output_cells)){
                    OutputCell::where([
                        'excel_version_id' => $version->id,
                        'cell' => $outputCell
                    ])->delete();
                }
            }

            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
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