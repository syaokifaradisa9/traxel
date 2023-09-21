<?php

namespace App\Models;

use App\Models\Calibrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupCalibrator extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'cell_ID',
        'cell_LH',
        'excel_version_id'
    ];

    public function calibrator(){
        return $this->hasMany(Calibrator::class);
    }
}
