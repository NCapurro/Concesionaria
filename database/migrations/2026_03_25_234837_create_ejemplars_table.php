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
        Schema::create('ejemplars', function (Blueprint $table) {
       $table->id();
    $table->foreignId('moto_id')->constrained('motos');
    $table->foreignId('sucursal_id')->constrained('sucursals');
    $table->foreignId('color_id')->constrained('colors');
    $table->foreignId('dueno_id')->nullable()->constrained('duenos'); // Null si es 0km del local
    
    $table->integer('anio_fabricacion');
    $table->integer('km')->default(0);
    $table->decimal('precio', 12, 2); // 12 dígitos total, 2 decimales
    $table->string('dominio')->nullable(); // La patente
    $table->string('nro_motor')->nullable();
    $table->string('nro_chasis')->nullable();
    $table->string('estado_venta')->default('Disponible'); // Disponible, Reservada, Vendida
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejemplars');
    }
};
