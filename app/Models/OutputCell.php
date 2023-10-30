<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputCell extends Model
{
    use HasFactory;
    protected $fillable = [
        'excel_version_id',
        'cell',
        'cell_name'
    ];

    public function output_cell_value(){
        return $this->hasMany(OutputCellValue::class);
    }

    public function excel_version(){
        return $this->belongsTo(ExcelVersion::class);
    }
}
