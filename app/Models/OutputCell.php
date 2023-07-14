<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputCell extends Model
{
    use HasFactory;
    protected $fillable = [
        'excel_version_id',
        'cell'
    ];

    public function output_cell_value(){
        return $this->hasMany(OutputCellValue::class);
    }
}
