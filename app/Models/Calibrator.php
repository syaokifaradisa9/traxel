<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calibrator extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'merk',
        'model_type',
        'model_type_name',
        'serial_number',
        'group_calibrator_id'
    ];

    protected $appends = [
        'full_name'
    ];

    public function group_calibrator(){
        return $this->belongsTo(GroupCalibrator::class);
    }

    public function getFullNameAttribute(){
        return $this->name 
                . ", Merek : " . $this->merk. ", " 
                . $this->model_type . " : " . $this->model_type_name . ", " 
                . "SN : " . $this->serial_number;
    }
}
