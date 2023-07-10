<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSchema extends Model
{
    use HasFactory;

    protected $appends = [
        'percentage'
    ];

    public function getPercentageAttribute(){
        return number_format(OutputCellValue::where('test_schema_id', $this->id)->where('is_verified', true)->count()/OutputCellValue::count(), 4) * 100;
    }
}
