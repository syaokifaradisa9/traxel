<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputCellValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'cell',
        'expected_value',
        'actual_value',
        'test_schema_id',
        'is_verified',
        'error_description',
    ];
}
