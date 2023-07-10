<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelVersion extends Model
{
    use HasFactory;
    protected $fillable = [
        'alkes_id',
        'version_name'
    ]; 

    public function input_cell(){
        return $this->hasMany(InputCell::class);
    }

    public function output_cell(){
        return $this->hasMany(OutputCell::class);
    }
}
