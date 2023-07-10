<?php

use App\Models\ExcelVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(ExcelVersion::class)->constrained();
            $table->string('simulation_date')->nullable();
            $table->string('simulation_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_schemas');
    }
};
