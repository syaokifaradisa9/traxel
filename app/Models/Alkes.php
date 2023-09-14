<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alkes extends Model
{
    use HasFactory;
    public function version(){
        return $this->hasMany(ExcelVersion::class);
    }
}
