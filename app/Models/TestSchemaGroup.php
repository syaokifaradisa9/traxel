<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSchemaGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'excel_version_id',
        'name'
    ];
}