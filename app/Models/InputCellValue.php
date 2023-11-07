<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputCellValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'input_cell_id',
        'value',
        'test_schema_id'
    ];

    public function input_cell(){
        return $this->belongsTo(InputCell::class);
    }

    public function test_schema(){
        return $this->belongsTo(TestSchema::class);
    }
}
