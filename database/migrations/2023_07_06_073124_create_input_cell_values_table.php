<?php

use App\Models\InputCell;
use App\Models\Scheme;
use App\Models\TestSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('input_cell_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InputCell::class)->constrained();
            $table->string('value');
            $table->foreignIdFor(TestSchema::class)->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('input_cell_values');
    }
};
