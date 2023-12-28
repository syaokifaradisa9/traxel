<?php

namespace App\Models;

use App\Models\GroupCalibrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExcelVersion extends Model
{
    use HasFactory;
    protected $fillable = [
        'alkes_id',
        'version_name'
    ];

    public function alkes(){
        return $this->belongsTo(Alkes::class);
    }

    public function input_cell(){
        return $this->hasMany(InputCell::class);
    }

    public function output_cell(){
        return $this->hasMany(OutputCell::class);
    }

    public function group_calibrator(){
        return $this->hasMany(GroupCalibrator::class);
    }

    public function test_schema_group(){
        return $this->hasMany(TestSchemaGroup::class);
    }
}
