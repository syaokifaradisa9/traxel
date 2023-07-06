<?php

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
            $table->string('cell');
            $table->string('value');
            $table->foreignIdFor(TestSchema::class)->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('output_cell_values');
    }
};
