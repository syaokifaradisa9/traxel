<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputCellValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'cell',
        'value',
        'test_schema_id'
    ];
}
