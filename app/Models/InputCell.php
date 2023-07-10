<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputCell extends Model
{
    use HasFactory;
    protected $fillable = [
        'cell',
        'cell_name',
        'excel_version_id'
    ];
}
