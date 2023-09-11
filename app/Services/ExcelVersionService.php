<?php

namespace App\Services;

use Exception;
use App\Models\Alkes;
use App\Models\InputCell;
use App\Models\OutputCell;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use App\Models\InputCellValue;
use App\Models\OutputCellValue;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelVersionService{
    private function getCellWithRedTextInSheet($file_path, $sheet_name){
        $spreadsheet = (new Xlsx())->load($file_path);
        $sheet = $spreadsheet->getSheetByName($sheet_name);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $input_merged_cells = [];
        foreach($sheet->getMergeCells() as $mergedCell){
            $first_cell = explode(":", $mergedCell)[0];
            $color_hex = $sheet->getCell($first_cell)->getStyle()->getFont()->getColor()->getRGB();

            if($color_hex == "FF0000"){
                $input_merged_cells[] = $mergedCell;
            }
        }

        $cell_with_red_text = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $cell = Coordinate::stringFromColumnIndex($column) . $row;
                
                $cell_has_merged = false;
                foreach($input_merged_cells as $merged_cell){
                    if($sheet->getCell($cell)->isInRange($merged_cell)){
                        $cell_has_merged = true;
                        $first_cell = explode(":", $merged_cell)[0];
                        if(!in_array($first_cell, $cell_with_red_text)){
                            $cell_with_red_text[] = $cell;
                        }
                    }
                }

                if(!$cell_has_merged){
                    $color_hex = $sheet->getCell($cell)->getStyle()->getFont()->getColor()->getRGB();
                    if($color_hex == "FF0000"){
                        $cell_with_red_text[] = $cell;
                    }
                }
            }
        }

        return $cell_with_red_text;
    }

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
    
            // Mengambil Cell Secara Otomatis Dari Excel
            $cell_inputs = $this->getCellWithRedTextInSheet($filePath . "/" . $fileName, "ID");
            $cell_outputs = $this->getCellWithRedTextInSheet($filePath . "/" . $fileName, "LH");

            $version = ExcelVersion::create([
                'version_name' => $data['version_name'],
                'alkes_id' => $alkesId
            ]);
    
            foreach($cell_inputs as $inputCell){
                InputCell::create([
                    'excel_version_id' => $version->id,
                    'cell' => $inputCell
                ]);
            }
    
            foreach($cell_outputs as $outputCell){
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

            $alkes = Alkes::find($alkesId);

            $fileName = $alkes->excel_name . "-" . $data['version_name']. ".xlsx";
            $filePath = public_path("excel");
        
            if(isset($data['file'])){
                $file = $data['file'];
                $file->move($filePath, $fileName);
            }

            $version = ExcelVersion::find($versionId);

            $cells = $this->getInputAndOutputCells($versionId);

            $previous_input_cells = explode(", ", $cells['input']);
            $previous_output_cells = explode(", ", $cells['output']);

            // Mengambil Cell Secara Otomatis Dari Excel
            $cell_inputs = $this->getCellWithRedTextInSheet($filePath . "/" . $fileName, "ID");
            $cell_outputs = $this->getCellWithRedTextInSheet($filePath . "/" . $fileName, "LH");

            foreach($cell_inputs as $inputCell){
                if(!in_array($inputCell, $previous_input_cells)){
                    InputCell::create([
                        'excel_version_id' => $version->id,
                        'cell' => $inputCell
                    ]);
                }
            }

            foreach($previous_input_cells as $inputCell){
                if(!in_array($inputCell, $cell_inputs)){
                    InputCell::where([
                        'excel_version_id' => $version->id,
                        'cell' => $inputCell
                    ])->delete();
                }
            }
    
            foreach($cell_outputs as $outputCell){
                if(!in_array($outputCell, $previous_output_cells)){
                    OutputCell::create([
                        'excel_version_id' => $version->id,
                        'cell' => $inputCell
                    ]);
                }
            }

            foreach($previous_output_cells as $outputCell){
                if(!in_array($outputCell, $cell_outputs)){
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

    public function deleteExcelVersion($versionId){
        try{
            DB::beginTransaction();

            $test_schemas = TestSchema::where('excel_version_id', $versionId)->get();
            foreach($test_schemas as $test_schema){
                InputCellValue::where('test_schema_id', $test_schema->id)->delete();
                OutputCellValue::where('test_schema_id', $test_schema->id)->delete();

                $test_schema->delete();
            }
            
            InputCell::where("excel_version_id", $versionId)->delete();
            OutputCell::where("excel_version_id", $versionId)->delete();

            ExcelVersion::find($versionId)->delete();

            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            return false;
        }
    }
}