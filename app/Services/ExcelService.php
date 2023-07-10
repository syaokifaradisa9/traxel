<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelService{
    public function getCalculateExcelValue($excel_path, $input, $sheetName){
        $excel = (new Xlsx())->load($excel_path);

        $sheet = $excel->getSheetByName('ID');

        foreach($input as $value){
            $sheet->getCell($value->cell)->setValue($value->value);
        }

        return $excel->getSheetByName($sheetName);
    }
}