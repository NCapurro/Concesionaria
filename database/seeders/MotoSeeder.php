<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MotoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Tablas Base (Catálogos)
        DB::table('marcas')->insert([
            ['nombre' => 'Honda',  'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Yamaha', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Kawasaki', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Zanella', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Motomel', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('categorias')->insert([
            ['descripcion' => 'Naked', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => 'Enduro', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => 'Pista', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => 'Street', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => 'Scooter', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('cilindradas')->insert([
            ['descripcion' => '110cc', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => '150cc', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => '250cc', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => '300cc', 'created_at' => $now, 'updated_at' => $now],
            ['descripcion' => '400cc', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('colors')->insert([
            ['nombre' => 'Rojo', 'codigo_hexadecimal' => '#FF0000', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Negro', 'codigo_hexadecimal' => '#000000', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Blanco', 'codigo_hexadecimal' => '#FFFFFF', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Azul', 'codigo_hexadecimal' => '#0000FF', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Verde', 'codigo_hexadecimal' => '#008000', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Concesionaria y Sucursales
        $concesionariaId = DB::table('concesionarias')->insertGetId(
    ['nombre' => 'Néstor Motos', 'created_at' => $now, 'updated_at' => $now]
);

        DB::table('sucursals')->insert([
            ['concesionaria_id' => $concesionariaId, 'nombre' => 'Paraná', 'direccion' => 'Cura Alvarez y Echague', 'telefono' => '+54 9 343 453-1520', 'created_at' => $now, 'updated_at' => $now],
            ['concesionaria_id' => $concesionariaId, 'nombre' => 'Santa Fe', 'direccion' => 'Mendoza 740', 'telefono' => '+54 9 3434 531520', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Dueños (Para las motos usadas)
        DB::table('duenos')->insert([
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'email' => 'juan.perez@example.com', 'dni' => '30111222', 'telefono' => '3434111222', 'direccion' => 'Calle Falsa 123', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'María', 'apellido' => 'Gómez', 'email' => 'maria.gomez@example.com', 'dni' => '31222333', 'telefono' => '3434222333', 'direccion' => 'San Martín 456', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Carlos', 'apellido' => 'López', 'email' => 'carlos.lopez@example.com', 'dni' => '32333444', 'telefono' => '3434333444', 'direccion' => 'Belgrano 789', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Ana', 'apellido' => 'Martínez', 'email' => 'ana.martinez@example.com', 'dni' => '33444555', 'telefono' => '3434444555', 'direccion' => 'Urquiza 101', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Pedro', 'apellido' => 'Sánchez', 'email' => 'pedro.sanchez@example.com', 'dni' => '34555666', 'telefono' => '3434555666', 'direccion' => 'Mitre 202', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Catálogo de Motos (Fichas técnicas)
        DB::table('motos')->insert([
            ['marca_id' => 1, 'categoria_id' => 2, 'cilindrada_id' => 3, 'modelo' => 'Tornado XR 250', 'created_at' => $now, 'updated_at' => $now], // Honda
            ['marca_id' => 2, 'categoria_id' => 4, 'cilindrada_id' => 2, 'modelo' => 'FZ-S FI', 'created_at' => $now, 'updated_at' => $now], // Yamaha
            ['marca_id' => 3, 'categoria_id' => 3, 'cilindrada_id' => 5, 'modelo' => 'Ninja 400', 'created_at' => $now, 'updated_at' => $now], // Kawasaki
            ['marca_id' => 4, 'categoria_id' => 5, 'cilindrada_id' => 1, 'modelo' => 'ZB 110 Base', 'created_at' => $now, 'updated_at' => $now], // Zanella
            ['marca_id' => 1, 'categoria_id' => 1, 'cilindrada_id' => 4, 'modelo' => 'CB 300F Twister', 'created_at' => $now, 'updated_at' => $now], // Honda
        ]);


        // 5. Ejemplares (El stock físico)
        // 5.1 -> 5 Motos 0KM (Sin dueño, sin dominio, 0km, año actual)
        DB::table('ejemplars')->insert([
            ['moto_id' => 1, 'sucursal_id' => 1, 'color_id' => 1, 'dueno_id' => null, 'anio_fabricacion' => 2024, 'km' => 0, 'precio' => 6500000, 'dominio' => null, 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 2, 'sucursal_id' => 2, 'color_id' => 2, 'dueno_id' => null, 'anio_fabricacion' => 2024, 'km' => 0, 'precio' => 3200000, 'dominio' => null, 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 3, 'sucursal_id' => 1, 'color_id' => 5, 'dueno_id' => null, 'anio_fabricacion' => 2024, 'km' => 0, 'precio' => 12500000, 'dominio' => null, 'estado_venta' => 'Reservado', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 4, 'sucursal_id' => 2, 'color_id' => 3, 'dueno_id' => null, 'anio_fabricacion' => 2024, 'km' => 0, 'precio' => 1100000, 'dominio' => null, 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 5, 'sucursal_id' => 1, 'color_id' => 1, 'dueno_id' => null, 'anio_fabricacion' => 2024, 'km' => 0, 'precio' => 5800000, 'dominio' => null, 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 5.2 -> 5 Motos Usadas (Con dueño, con dominio, km > 0, años anteriores)
        DB::table('ejemplars')->insert([
            ['moto_id' => 1, 'sucursal_id' => 2, 'color_id' => 2, 'dueno_id' => 1, 'anio_fabricacion' => 2021, 'km' => 15500, 'precio' => 4500000, 'dominio' => 'A112BCD', 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 2, 'sucursal_id' => 1, 'color_id' => 4, 'dueno_id' => 2, 'anio_fabricacion' => 2019, 'km' => 28000, 'precio' => 2100000, 'dominio' => 'A098XYZ', 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 3, 'sucursal_id' => 2, 'color_id' => 2, 'dueno_id' => 3, 'anio_fabricacion' => 2022, 'km' => 8500, 'precio' => 9800000, 'dominio' => 'A155QWE', 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 4, 'sucursal_id' => 1, 'color_id' => 2, 'dueno_id' => 4, 'anio_fabricacion' => 2020, 'km' => 12000, 'precio' => 750000, 'dominio' => 'A100LKM', 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
            ['moto_id' => 5, 'sucursal_id' => 2, 'color_id' => 1, 'dueno_id' => 5, 'anio_fabricacion' => 2023, 'km' => 3200, 'precio' => 4900000, 'dominio' => 'A188POP', 'estado_venta' => 'Disponible', 'created_at' => $now, 'updated_at' => $now],
        ]);

        
        $now = now();

for ($i = 6; $i <= 25; $i++) {
    DB::table('ejemplars')->insert([
        'moto_id' => rand(1, 5), // moto_id incremental
        'sucursal_id' => ($i % 2) + 1, // alterna entre sucursal 1 y 2
        'color_id' => ($i % 5) + 1, // rota entre 1 y 5
        'dueno_id' => null,
        'anio_fabricacion' => 2024,
        'km' => rand(0, 500), // algunos con km iniciales
        'precio' => rand(1000000, 15000000), // rango de precios
        'dominio' => null,
        'estado_venta' => ($i % 3 == 0) ? 'Reservado' : 'Disponible', // alterna estados
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}



        
        // 6. Imágenes locales
// 6. Primero creamos TODAS las imágenes
        for ($i = 1; $i <= 10; $i++) {
            DB::table('imagens')->insert([
                'url' => 'motos/moto_' . $i . '.webp', 
                'created_at' => $now, 
                'updated_at' => $now
            ]);
        }
        
        // 7. Después asignamos las relaciones cruzadas
        for ($i = 1; $i <= 10; $i++) {
            DB::table('ejemplar_imagen')->insert([
                ['ejemplar_id' => $i, 'imagen_id' => $i, 'created_at' => $now, 'updated_at' => $now],
                ['ejemplar_id' => $i, 'imagen_id' => $i == 10 ? 1 : $i + 1, 'created_at' => $now, 'updated_at' => $now]
            ]);
        }
    }
}