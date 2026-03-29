<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'nombre', 'telefono', 'email', 'interes', 'mensaje', 'estado'
    ];
}
