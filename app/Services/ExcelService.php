<?php

namespace App\Services;

use Exception;
use TypeError;
use DivisionByZeroError;
use App\Models\ExcelVersion;
use App\Models\InputCellValue;
use App\Models\OutputCellValue;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelService{
    public function getCalculateExcelValue($excel_path, $input, $sheetName){
        $excel = (new Xlsx())->load($excel_path);

        $sheet = $excel->getSheetByName('ID');

        foreach($input as $value){
            $sheet->getCell($value->input_cell->cell)->setValue($value->value);
        }

        return $excel->getSheetByName($sheetName);
    }

    public function getAllSheetNames($versionId){
        # Load Excel
        $version = ExcelVersion::with('alkes')->find($versionId);
        $excel_name = $version->alkes->excel_name;
        $version_name = $version->version_name;
        $spreadsheet = (new Xlsx())->load("excel/{$excel_name}-{$version_name}.xlsx");

        # Pengambilan Semua Nama Sheet
        $allSheets = $spreadsheet->getAllSheets();


        return $allSheets;
    }

    public function getExcelCellValue($versionId, $schemaId, $selected_sheet){
        if(!$selected_sheet){
            return [];
        }

        # Load Excel
        $version = ExcelVersion::with('alkes')->find($versionId);
        $excel_name = $version->alkes->excel_name;
        $version_name = $version->version_name;
        $spreadsheet = (new Xlsx())->load("excel/{$excel_name}-{$version_name}.xlsx");
        
        # Melakukan Input Data
        $sheet = $spreadsheet->getSheetByName('ID');
        $input = InputCellValue::where('test_schema_id', $schemaId)->get();
        foreach($input as $value){
            $sheet->getCell($value->input_cell->cell)->setValue((string) $value->value);
        }
        
        # Pengambilan Semua Nama Sheet
        $allSheets = $spreadsheet->getAllSheets();

        # Pengambilan Semua Cell Pada Setiap Sheet
        $excel_values = [];
        $skip_values = [
            '', ":", "no", "no.", "i.", "i", "ii", "ii.", "iii", "iii.", "iv", "iv.", "v", "v.", "vi", "vi.", "vii", "vii.", "viii", "viii.", "ix", "ix.",
            'merek', 'model/tipe', 'kapasitas', 'tanggal penerimaan alat', "1. suhu", "1. suhu", "3. tegangan jala-jala", "resistansi isolasi", "( mm )",
            'nama ruang', 'metode kerja', 'keterangan', 'kondisi ruang', 'suhu ruang', 'kelembaban', 'tekanan udara', "stdev", "°c", "%rh", "tahun", "drift",
            'petugas kalibrasi', 'parameter', "setting alat", "alat yang digunakan", "1. fisik", "pemeriksaan kondisi fisik dan fungsi alat", "setting panjang",
            "2. fungsi", "kesimpulan", "no. seri", "tanggal kalibrasi", "awal", "akhir", "pengujian kinerja", "tempat kalibrasi", "tanggal pembuatan laporan",
            "setting standar", "hasil pengamatan", "pembacaan standar", 'rata - rata', 'terkoreksi', 'hasil ukur', "µa", "mΩ", "Ω", "2. kelembaban",
            "pengujian keselamatan listrik", "rata - rata terkoreksi", "drift standar", "toleransi", 'input nc', "pembacaan pada standar (mm)", "volt",
            "koreksi caliper", "( mv )", "setting sine", "setting heart rate", "( bpm )", "setting amplitude", "setting square", "mv", "coreksi",
            "reading", "of", "suhu", "no urut alat", "konversi text", "rata-rata terkoreksi", "(", "±", ")", "current leakage", "( ua )", "( mΩ )", "resistance", 
            "( Ω )", "koreksi esa", "setting vac", "main-pe", "( v )", "pembacaan terkoreksi", "driff", "arus bocor", "over", "(v)", "ambang batas", "yang diijinkan",
            "rata-rata", "pembacaan standar (mm)", "koreksi (mm)", "koreksi relatif (%)", "u95 std", "kosong", "drift (bpm)", "drift (s)", "interpolasi arus bocor",
            "toleransi (%)", "ketidakpastian pengukuran (%)", 'tanggal', "jumlah", "komponen", 'mm', 'satuan', "drift (mm)", "tegangan u 95", "nama pemilik", "alamat pemilik",
            'faktor cakupan', 'bpm', '=', "k", "ui", "ci", "hz", "uici", "(uici)^2", "mm/mv", "( hz )", "u95 (s)", "hasil", "rata-rata standar", "nomor seri", "resolusi",
            "2015", "2016", "2017", "2018", "2019", "2020", "2021", "2022", "lokasi kalibrasi", "nomor order", "bahan", "nama alat:", "model/tipe", "merk", "serial number",
            'kunci kop sertifikat', "gram", "µg", "mg", "µl", "mg/ml", "random error", "cv", "lop", "uncertainty", "koreksi sertifikat", "bejana kosong", "volume (µl)",
            "correction (µl)", "random error (sr)", "(g)", "sistematic error", "reproducibility (repeatability)", "readability(ketelitian/akurasi timbangan)", "konversi",
            "rata rata pembacaan standar (g)", "interpolasi koreksi", "g/ml", "u95", "nl", "pembagi"
        ];
        foreach($allSheets as $sheet){
            if(in_array($sheet->getTitle(), $selected_sheet)){
                $sheet = $spreadsheet->getSheetByName($sheet->getTitle());
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

                $inner_excel_values = [];
                for ($row = 1; $row <= $highestRow; $row++) {
                    for ($column = 1; $column <= $highestColumnIndex; $column++) {
                        try{
                            $cell = Coordinate::stringFromColumnIndex($column) . $row;
                            $value = $sheet->getCell($cell)->getCalculatedValue();
                        
                            if(!in_array(strtolower(trim($value)), $skip_values)){
                                $inner_excel_values[] = [
                                    "cell" => $cell,
                                    'value' => (string) $value
                                ];
                            }
                        }catch (DivisionByZeroError $e) {
                            $inner_excel_values[] = [
                                "cell" => $cell,
                                'value' => "#DIV/0!"
                            ];
                        }catch(Exception $e){
                            $inner_excel_values[] = [
                                "cell" => $cell,
                                'value' => $e->getMessage()
                            ];
                        }catch(TypeError $e){
                            $inner_excel_values[] = [
                                "cell" => $cell,
                                'value' => $e->getMessage()
                            ];
                        }
                    }
                }

                usort($inner_excel_values, function($a, $b) {
                    return strcmp($a['cell'], $b['cell']);
                });
                $excel_values[$sheet->getTitle()] = $inner_excel_values;
            }
        }
        ksort($excel_values);

        return $excel_values;
    }

    public function getErrorCellInResultSheet($schemaId){
        $output_cell_values = OutputCellValue::with('output_cell')->where('test_schema_id', $schemaId)->get();
        $error_result_cells = [];
        foreach($output_cell_values as $output_cell_value){
            if($output_cell_value->is_verified == 0){
                $error_result_cells[] = [
                    'cell' => $output_cell_value->output_cell->cell,
                    "expected_value" => $output_cell_value->expected_value,
                ];
            }
        }

        return collect($error_result_cells);
    }
}