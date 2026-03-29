<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Imagen extends Model
{
    protected $table = 'imagens';
    protected $fillable = ['url'];

    public function ejemplares(): BelongsToMany
    {
        return $this->belongsToMany(Ejemplar::class, 'ejemplar_imagen', 'imagen_id', 'ejemplar_id');
    }
}