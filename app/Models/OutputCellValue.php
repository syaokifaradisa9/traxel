<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputCellValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'output_cell_id',
        'expected_value',
        'actual_value',
        'test_schema_id',
        'is_verified',
        'error_description',
    ];

    public function test_schema(){
        return $this->belongsTo(TestSchema::class);
    }

    public function output_cell(){
        return $this->belongsTo(OutputCell::class);
    }
}
