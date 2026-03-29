<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cilindrada extends Model
{
    protected $table = 'cilindradas';
    protected $fillable = ['descripcion'];

    public function motos(): HasMany
    {
        return $this->hasMany(Moto::class);
    }
}