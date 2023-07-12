<?php

use App\Models\OutputCell;
use App\Models\TestSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('output_cell_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OutputCell::class)->constrained();
            $table->string('expected_value');
            $table->string('actual_value')->nullable();
            $table->foreignIdFor(TestSchema::class)->constrained();
            $table->boolean('is_verified')->default(false);
            $table->text('error_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('output_cell_values');
    }
};
