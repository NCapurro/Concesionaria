<?php

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
        Schema::create('ejemplar_imagen', function (Blueprint $table) {
            $table->id();
    $table->foreignId('ejemplar_id')->constrained('ejemplars')->cascadeOnDelete();
    $table->foreignId('imagen_id')->constrained('imagens')->cascadeOnDelete();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejemplar_imagen');
    }
};
