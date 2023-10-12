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

    public function test_schema(){
        return $this->hasMany(TestSchema::class);
    }
}
