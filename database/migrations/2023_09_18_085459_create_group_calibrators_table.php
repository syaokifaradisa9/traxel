<?php

use App\Models\ExcelVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_calibrators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cell_ID');
            $table->string('cell_LH');
            $table->foreignIdFor(ExcelVersion::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_calibrators');
    }
};
