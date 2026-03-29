<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursals';
    protected $fillable = ['concesionaria_id', 'nombre', 'direccion'];

    public function concesionaria(): BelongsTo
    {
        return $this->belongsTo(Concesionaria::class);
    }

    public function ejemplares(): HasMany
    {
        return $this->hasMany(Ejemplar::class);
    }
}