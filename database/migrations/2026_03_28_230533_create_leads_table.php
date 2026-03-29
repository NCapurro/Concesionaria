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
    Schema::create('leads', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('telefono');
        $table->string('email')->nullable();
        $table->string('interes');
        $table->text('mensaje')->nullable();
        
        // Campo clave para el negocio: para saber si ya lo atendieron
        $table->string('estado')->default('Nuevo'); // Nuevo, Contactado, Vendido, Perdido
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
