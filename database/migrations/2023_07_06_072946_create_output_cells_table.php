<?php

use App\Models\Alkes;
use App\Models\ExcelVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('output_cells', function (Blueprint $table) {
            $table->id();
            $table->string('cell');
            $table->foreignIdFor(ExcelVersion::class)->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('output_cells');
    }
};
