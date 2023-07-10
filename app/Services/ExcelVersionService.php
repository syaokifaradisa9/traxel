<?php

namespace App\Services;

use App\Models\ExcelVersion;

class ExcelVersionService{
    public function getVersionByAlkesId($alkesId){
        return ExcelVersion::with(['input_cell', 'output_cell'])->where('alkes_id', $alkesId)->get();
    }
}