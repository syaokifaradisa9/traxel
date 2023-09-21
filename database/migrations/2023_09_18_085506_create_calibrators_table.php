<?php

use App\Models\GroupCalibrator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calibrators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('merk');
            $table->string('model_type');
            $table->string('model_type_name');
            $table->string('serial_number');
            $table->foreignIdFor(GroupCalibrator::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibrators');
    }
};
