<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestSchema extends Model
{
    use HasFactory;
    protected $fillable = [
        'test_schema_group_id',
        'name'
    ];

    public function test_schema_group(){
        return $this->belongsTo(TestSchemaGroup::class);
    }

    public function output_cell_value(){
        return $this->hasMany(OutputCellValue::class);
    }

    protected $appends = [
        'percentage',
        'simulation_days_ago',
        "can_generate"
    ];

    public function getPercentageAttribute(){
        $total_done = OutputCellValue::where('test_schema_id', $this->id)->where('is_verified', true)->count();
        $total_test = OutputCellValue::where('test_schema_id', $this->id)->count();

        $percentage = $total_test == 0 ? 0 : $total_done/$total_test;
        return number_format($percentage, 4) * 100;
    }

    public function getSimulationDaysAgoAttribute(){
        $yourDate = Carbon::createFromFormat('Y-m-d', $this->simulation_date);
        $today = Carbon::today();

        return $yourDate->diffInDays($today);
    }

    public function getCanGenerateAttribute(){
        $groupCalibratorInResultCount = $this->test_schema_group->excel_version->group_calibrator->where("cell_LH", "!=", "")->count();
        return OutputCellValue::where('test_schema_id', $this->id)->count() == $groupCalibratorInResultCount;
    }
}
