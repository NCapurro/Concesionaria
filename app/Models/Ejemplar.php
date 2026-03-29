<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ejemplar extends Model
{
    protected $table = 'ejemplars';
    
    protected $fillable = [
        'moto_id', 'sucursal_id', 'color_id', 'dueno_id', 
        'anio_fabricacion', 'km', 'precio', 'dominio', 
        'nro_motor', 'nro_chasis', 'estado_venta'
    ];

    public function moto(): BelongsTo
    {
        return $this->belongsTo(Moto::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function dueno(): BelongsTo
    {
        return $this->belongsTo(Dueno::class);
    }

    // Relación Muchos a Muchos con las Imágenes
    public function imagenes(): BelongsToMany
    {
        return $this->belongsToMany(Imagen::class, 'ejemplar_imagen', 'ejemplar_id', 'imagen_id');
    }
}