<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concesionaria extends Model
{
    protected $table = 'concesionarias';
    protected $fillable = ['nombre'];

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }
}