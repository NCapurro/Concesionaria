<?php

namespace App\Http\Controllers;

use App\Models\Ejemplar;
use App\Models\Sucursal;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Traemos las sucursales para el mapa y contacto
        $sucursales = Sucursal::all()->map(function ($sucursal) {
            return [
                'nombre' => $sucursal->nombre,
                'direccion' => $sucursal->direccion,
                // Datos hardcodeados por ahora para la demo comercial
                'telefono' => '+54 9 343 4531522',
                'whatsapp' => '5493434531522',
                'instagram' => 'ncsoftware_dev',
                'lat' => $sucursal->nombre === 'Paraná' ? -31.7396522 : -31.646500596262143,
                'lng' => $sucursal->nombre === 'Paraná' ? -60.5203775 : -60.71655290567032,
            ];
        });

        // 2. Traemos el stock con todas sus relaciones cargadas
        $ejemplares = Ejemplar::with([
            'moto.marca', 
            'moto.categoria', 
            'moto.cilindrada', 
            'color', 
            'sucursal', 
            'imagenes'
        ])->get();

        // 3. Mapeamos la colección al formato exacto que espera tu frontend
        $motosMapeadas = $ejemplares->map(function ($ejemplar) {
            return [
                'id_ejemplar' => $ejemplar->id,
                'precio' => $ejemplar->precio,
                'km' => $ejemplar->km,
                'anio_fabricacion' => $ejemplar->anio_fabricacion,
                'dominio' => $ejemplar->dominio,
                'estado_venta' => $ejemplar->estado_venta,
                'color' => $ejemplar->color->color,
                'sucursal' => $ejemplar->sucursal->nombre,
                'moto' => [
                    'marca' => $ejemplar->moto->marca->nombre,
                    'modelo' => $ejemplar->moto->modelo,
                    'categoria' => $ejemplar->moto->categoria->descripcion,
                    'cilindrada' => $ejemplar->moto->cilindrada->descripcion,
                ],
                // Pluck saca solo la columna 'url' de la colección de imágenes
                'imagenes' => $ejemplar->imagenes->map(function ($imagen) {
                     // asset() le agrega el "http://localhost:8000/" automáticamente
                     return asset('storage/' . $imagen->url);
                    })->toArray(),
            ];
        });

        // 4. Retornamos la vista de Inertia pasándole los props
        return Inertia::render('Welcome', [
            'motos' => $motosMapeadas,
            'sucursales' => $sucursales
        ]);
    }
}