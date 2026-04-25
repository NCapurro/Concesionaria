<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Ejemplar;
use App\Models\Moto;
use App\Models\Sucursal;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Cilindrada;
use App\Models\Color;

// Asegurate de importar los modelos que uses

class AdminMotoController extends Controller
{
   public function index()
    {
        // ACÁ ESTÁ LA MAGIA: Traemos el árbol de relaciones completo, incluyendo el color si es una tabla.
        $motos = Ejemplar::with([
            'moto.marca', 
            'moto.categoria', 
            'moto.cilindrada', 
            'sucursal', 
            'color' // Si color es una tabla relacionada, sumalo acá
        ])->orderBy('id', 'desc')->get();
        
        $sucursales = Sucursal::all(); 

        $motos_catalogo = Moto::with(['marca', 'categoria', 'cilindrada'])->get()->map(function ($moto) {
            return [
                'id' => $moto->id,
                'marca' => $moto->marca->nombre ?? '', 
                'modelo' => $moto->modelo,
                'categoria' => $moto->categoria->descripcion ?? '', // Perfecto esto
                'cilindrada' => $moto->cilindrada->descripcion ?? '', // Perfecto esto
            ];
        });

        return Inertia::render('Admin/AdminStock', [
            'motos' => $motos,
            'sucursales' => $sucursales,
            'motos_catalogo' => $motos_catalogo
        ]);
    }

    public function store(Request $request)
{
    // 1. Validar los datos (igual que antes)
    $validated = $request->validate([
        'marca' => 'required|string',
        'modelo' => 'required|string',
        'categoria' => 'required|string',
        'cilindrada' => 'required|string',
        'precio' => 'required|numeric',
        'km' => 'required|numeric',
        'anio_fabricacion' => 'required|integer',
        'color' => 'required|string',
        'sucursal_id' => 'required|integer', // Acordate que ahora mandamos el ID de sucursal
        'estado_venta' => 'required|string',
        'dominio' => 'nullable|string',
    ]);

    // 2. Encontrar o Crear las entidades relacionadas (La magia relacional)
    // Busca por el nombre, si no existe lo inserta en la tabla y te devuelve el objeto.
    $marca = Marca::firstOrCreate(['nombre' => $validated['marca']]);
    $categoria = Categoria::firstOrCreate(['descripcion' => $validated['categoria']]);
    $cilindrada = Cilindrada::firstOrCreate(['descripcion' => $validated['cilindrada']]);
    $color = Color::firstOrCreate(['nombre' => $validated['color']]);

    // 3. Encontrar o Crear la Moto usando los IDs que obtuvimos recién
    $moto = Moto::firstOrCreate(
        ['marca_id' => $marca->id, 'modelo' => $validated['modelo']],
        ['categoria_id' => $categoria->id, 'cilindrada_id' => $cilindrada->id]
    );

    // 4. Finalmente, crear el Ejemplar físico en la concesionaria
    Ejemplar::create([
        'moto_id' => $moto->id,
        'precio' => $validated['precio'],
        'km' => $validated['km'],
        'anio_fabricacion' => $validated['anio_fabricacion'],
        'color_id' => $color->id, // Usamos el ID del color
        'sucursal_id' => $validated['sucursal_id'],
        'estado_venta' => $validated['estado_venta'],
        'dominio' => $validated['dominio'],
    ]);

    return redirect()->back()->with('message', 'Moto registrada correctamente');
}

   public function update(Request $request, $id)
    {
        // 1. Validar los datos (usamos strings para atajar los textos del form, excepto en sucursal_id)
        $validated = $request->validate([
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'categoria' => 'required|string',
            'cilindrada' => 'required|string',
            'precio' => 'required|numeric',
            'km' => 'required|numeric',
            'anio_fabricacion' => 'required|integer',
            'color' => 'required|string',
            'sucursal_id' => 'required|integer',
            'estado_venta' => 'required|string',
            'dominio' => 'nullable|string',
        ]);

        // 2. Buscar el Ejemplar físico que vamos a editar
        $ejemplar = Ejemplar::findOrFail($id);

        // 3. Encontrar o Crear las entidades (Traductor de Textos a IDs)
        $marca = Marca::firstOrCreate(['nombre' => $validated['marca']]);
        $categoria = Categoria::firstOrCreate(['descripcion' => $validated['categoria']]);
        $cilindrada = Cilindrada::firstOrCreate(['descripcion' => $validated['cilindrada']]);
        $color = Color::firstOrCreate(['nombre' => $validated['color']]);

        // 4. Encontrar o Crear la Moto (Catálogo) usando los IDs
        $moto = Moto::firstOrCreate(
            ['marca_id' => $marca->id, 'modelo' => $validated['modelo']],
            ['categoria_id' => $categoria->id, 'cilindrada_id' => $cilindrada->id]
        );

        // 5. Actualizar el Ejemplar con los IDs relacionales y los valores nuevos
        $ejemplar->update([
            'moto_id' => $moto->id,
            'precio' => $validated['precio'],
            'km' => $validated['km'],
            'anio_fabricacion' => $validated['anio_fabricacion'],
            'color_id' => $color->id, // Usamos el ID del color relacional
            'sucursal_id' => $validated['sucursal_id'], // Usamos el ID de la sucursal
            'estado_venta' => $validated['estado_venta'],
            'dominio' => $validated['dominio'],
        ]);

        return redirect()->back()->with('message', 'Moto actualizada correctamente');
    }
    public function destroy($id)
    {
        Ejemplar::findOrFail($id)->delete();
        return redirect()->back()->with('message', 'Moto eliminada');
    }
}