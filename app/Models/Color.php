<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    protected $table = 'colors';
    protected $fillable = ['nombre', 'codigo_hexadecimal'];

    public function ejemplares(): HasMany
    {
        return $this->hasMany(Ejemplar::class);
    }
}