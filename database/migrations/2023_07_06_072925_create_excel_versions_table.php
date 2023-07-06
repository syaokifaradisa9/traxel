<?php

use App\Models\Alkes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Alkes::class)->constrained();
            $table->string('version_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_versions');
    }
};
