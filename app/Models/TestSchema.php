<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestSchema extends Model
{
    use HasFactory;
    protected $fillable = [
        'excel_version_id',
        'name'
    ];

    protected $appends = [
        'percentage',
        'simulation_days_ago'
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
}
