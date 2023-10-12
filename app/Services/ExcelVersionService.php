<?php

namespace App\Services;

use Exception;
use ZipArchive;
use App\Models\Alkes;
use App\Models\Calibrator;
use App\Models\InputCell;
use App\Models\OutputCell;
use App\Models\TestSchema;
use App\Models\ExcelVersion;
use App\Models\GroupCalibrator;
use App\Models\InputCellValue;
use App\Models\OutputCellValue;
use App\Models\TestSchemaGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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

    private function getInputCellValueWithRedTextInSheet($file_path, $input_cells){
        $spreadsheet = (new Xlsx())->load($file_path);
        $sheet = $spreadsheet->getSheetByName("ID");

        $input_cell_values = [];
        foreach ($input_cells as $input_cell){
            $value = $sheet->getCell($input_cell)->getFormattedValue();

            $input_cell_values[] = [
                "cell" => $input_cell,
                "value" => $value,
                
            ];
        }

        return $input_cell_values;
    }

    public function getInputCellValueWithRedTextInSheetByExcelversion($id){
        $excel_version = ExcelVersion::find($id);
        $alkes = $excel_version->alkes;

        $file_name = $alkes->excel_name . "-" . $excel_version->version_name . ".xlsx";

        $filePath = public_path("excel");

        $cell_inputs = $this->getCellWithRedTextInSheet($filePath . "/" . $file_name, "ID");
        return $this->getInputCellValueWithRedTextInSheet(
            $filePath . "/" . $file_name,
            $cell_inputs
        );
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
    
            $cell_inputs = $this->getCellWithRedTextInSheet($filePath . "/" . $fileName, "ID");
            $cell_outputs = $this->getCellWithRedTextInSheet($filePath . "/" . $fileName, "LH");

            dd($alkesId, implode('", "', $cell_inputs), implode('", "', $cell_outputs));

            $version = ExcelVersion::create([
                'version_name' => $data['version_name'],
                'alkes_id' => $alkesId
            ]);

            foreach($cell_inputs as $cell_input){
                InputCell::create([
                    'excel_version_id' => $version->id,
                    'cell' => $cell_input
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

    public function exportExcelVersion($alkesId, $versionId){
        $version      = ExcelVersion::with('group_calibrator')->find($versionId);
        $input_cells  = InputCell::select('cell', 'cell_name')->where('excel_version_id', $versionId)->get();
        $output_cells = OutputCell::select('cell', 'cell_name')->where('excel_version_id', $versionId)->get();
        
        $group_test_schemas = [];
        $groupSchemes = TestSchemaGroup::where('excel_version_id', $versionId)->get();
        foreach ($groupSchemes as $groupScheme){
            $groupSchema = [
                "name" => $groupScheme->name
            ];
            foreach($groupScheme->test_schema as $test_schema){
                $schema = [
                    'name' => $test_schema->name
                ];

                $inputCellValues = [];
                $input_cell_values = InputCellValue::with('input_cell')->where('test_schema_id', $test_schema->id)->get();
                
                foreach($input_cell_values as $cell_value){
                    $inputCellValues[] = [
                        "cell" => $cell_value->input_cell->cell,
                        "value" => $cell_value->value
                    ];
                }

                $schema['input_cell_values'] = $inputCellValues;

                $outputCellValues = [];
                $output_cell_value = OutputCellValue::with('output_cell')->where('test_schema_id', $test_schema->id)->get();
                
                foreach($output_cell_value as $cell_value){
                    $outputCellValues[] = [
                        "cell" => $cell_value->output_cell->cell,
                        "value" => $cell_value->expected_value
                    ];
                }

                $schema['output_cell_value'] = $outputCellValues;
                $groupSchema["schema"][] = $schema;
            }

            $group_test_schemas[] = $groupSchema;
        }

        $groupCalibrators = [];
        foreach($version->group_calibrator as $groupCalibrator){
            $group_calibrator = [
                "name" => $groupCalibrator->name,
                'cell_ID' => $groupCalibrator->cell_ID,
                'cell_LH' => $groupCalibrator->cell_LH
            ];
            foreach($groupCalibrator->calibrator as $calibrator){
                $group_calibrator['calibrator'][] = [
                    "name" => $calibrator->name,
                    "merk" => $calibrator->merk,
                    'model_type' => $calibrator->model_type,
                    'model_type_name' => $calibrator->model_type_name,
                    'serial_number' => $calibrator->serial_number,
                ];
            }

            $groupCalibrators[] = $group_calibrator;
        }

        $version_json = [];

        $version_json['alkes_id'] = $version->alkes_id;
        $version_json['name'] = $version->version_name;
        $version_json['calibrator'] = $groupCalibrators;
        $version_json['cell_input'] = $input_cells;
        $version_json['cell_output'] = $output_cells;
        $version_json['schema_group'] = $group_test_schemas;
        
        $alkes = Alkes::find($alkesId);
        
        $jsonData = json_encode($version_json);
        
        // Simpan JSON dalam file temporary
        $jsonFilename = $alkes->name . " Versi " . $version->version_name . '.json';
        $jsonTempFile = tempnam(sys_get_temp_dir(), 'json_export');
        file_put_contents($jsonTempFile, $jsonData);

        // Buat objek ZipArchive
        $zip = new ZipArchive();
        $zipFilename = public_path("excel/" . $alkes->excel_name . "-" . $version->version_name . ".zip");

        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Tambahkan file JSON ke dalam zip
            $zip->addFile($jsonTempFile, $jsonFilename);

            // Tambahkan file lainnya dari direktori public
            $excelPath = public_path("excel/" . $alkes->excel_name . "-" . $version->version_name.".xlsx");
            $zip->addFile($excelPath, $alkes->excel_name . "-" . $version->version_name.".xlsx");

            // Tutup zip
            $zip->close();

            // Hapus file temporary JSON
            unlink($jsonTempFile);

            // Set header untuk mengirim zip
            header('Content-Type: application/zip');
            header("Content-Disposition: attachment; filename=" . basename($zipFilename));

            // Keluarkan isi zip
            readfile($zipFilename);
            exit;
        } else {
            // Gagal membuka zip, berikan respon yang sesuai
            echo "Gagal membuat file ZIP.";
        }
    }

    public function importExcelVersion($file){
        try{
            DB::beginTransaction();

            File::cleanDirectory(public_path("temp"));
            $zip = new ZipArchive();

            $file->move(
                public_path("temp"), 
                "zipper.zip"
            );

            if($zip->open(public_path("temp/zipper.zip"))){
                $zip->extractTo(public_path("temp/"));
            }

            $data = '';
            $files = File::allFiles(public_path("temp"));
            foreach($files as $file){
                if($file->getExtension() == "json"){
                    $fileContents = file_get_contents(public_path("temp/" . $file->getRelativePathname()));
                    $data = json_decode($fileContents, true);
                }else if($file->getExtension() == "xls" || $file->getExtension() == "xlsx"){
                    File::copy(public_path("temp/" . $file->getRelativePathname()), public_path("excel/" . $file->getRelativePathname()));
                }
            }

            // Menyimpan Versi
            $alkes_id = $data['alkes_id'];

            $version_name = $data['name'];

            $excel_version = ExcelVersion::create([
                'alkes_id' => $alkes_id,
                'version_name' => $version_name
            ]);
    
            // Menyimpan Cell Input
            $cell_inputs = $data['cell_input'];
            foreach($cell_inputs as $cell_input){
                InputCell::create([
                    'cell' => $cell_input['cell'],
                    'cell_name' => $cell_input['cell_name'],
                    'excel_version_id' => $excel_version->id
                ]);
            }
         
            // Menyimpan Cell Output
            $cell_outputs = $data['cell_output'];
            foreach($cell_outputs as $cell_output){
                OutputCell::create([
                    'cell' => $cell_output['cell'],
                    'excel_version_id' => $excel_version->id
                ]);
            }
    
            foreach($data['calibrator'] as $groupCalibrator){
                $group_calibrator = GroupCalibrator::create([
                    'name' => $groupCalibrator['name'],
                    'cell_ID' => $groupCalibrator['cell_ID'],
                    'cell_LH' => $groupCalibrator['cell_LH'],
                    'excel_version_id' => $excel_version->id
                ]);

                foreach($groupCalibrator['calibrator'] as $calibrator){
                    Calibrator::create([
                        'name' => $calibrator['name'],
                        'merk' => $calibrator['merk'],
                        'model_type' => $calibrator['model_type'],
                        'model_type_name' => $calibrator['model_type_name'],
                        'serial_number' => $calibrator['serial_number'],
                        'group_calibrator_id' => $group_calibrator->id
                    ]);
                }
            }

            $schema_groups = $data['schema_group'];
            foreach($schema_groups as $schema_group){
                $schemaGroup = TestSchemaGroup::create([
                    'excel_version_id' => $excel_version->id,
                    'name' => $schema_group['name']
                ]);

                foreach($schema_group['schema'] as $testSchema){
                    $test_schema = TestSchema::create([
                        'name' => $testSchema['name'],
                        'test_schema_group_id' => $schemaGroup->id
                    ]);

                    foreach($testSchema['input_cell_values'] as $input_cell){
                        $cell_id = InputCell::where('excel_version_id', $excel_version->id)
                                            ->where('cell', $input_cell['cell'])
                                            ->first()->id;
        
                        InputCellValue::create([
                            'input_cell_id' => $cell_id,
                            'value' => $input_cell['value'],
                            'test_schema_id' => $test_schema->id
                        ]);
                    }

                    foreach($testSchema['output_cell_value'] as $input_cell){
                        $cell_id = OutputCell::where('excel_version_id', $excel_version->id)
                                            ->where('cell', $input_cell['cell'])
                                            ->first()->id;
                        
                        OutputCellValue::create([
                            'output_cell_id' => $cell_id,
                            'expected_value' => $input_cell['value'] ?? '',
                            'actual_value' => '',
                            'test_schema_id' => $test_schema->id,
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
}