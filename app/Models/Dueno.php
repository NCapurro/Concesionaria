<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dueno extends Model
{
    protected $table = 'duenos';
    protected $fillable = ['nombre', 'apellido', 'email', 'dni', 'telefono', 'direccion', 'fecha_nacimiento'];

    public function ejemplares(): HasMany
    {
        return $this->hasMany(Ejemplar::class);
    }
}