<?php

use App\Models\ExcelVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_schema_groups', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignIdFor(ExcelVersion::class)->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_schema_groups');
    }
};
