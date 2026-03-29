<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = ['descripcion'];

    public function motos(): HasMany
    {
        return $this->hasMany(Moto::class);
    }
}