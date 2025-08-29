<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de colores.
     */
    public function run(): void
    {
        $colores = [
            [
                'color' => 'Azul',
                'codigo' => '#0066CC',
                'estado' => true,
            ],
            [
                'color' => 'Verde',
                'codigo' => '#00CC66',
                'estado' => true,
            ],
            [
                'color' => 'Rojo',
                'codigo' => '#CC0000',
                'estado' => true,
            ],
            [
                'color' => 'Amarillo',
                'codigo' => '#FFCC00',
                'estado' => true,
            ],
            [
                'color' => 'Morado',
                'codigo' => '#6600CC',
                'estado' => true,
            ],
            [
                'color' => 'Naranja',
                'codigo' => '#FF6600',
                'estado' => true,
            ],
        ];

        foreach ($colores as $color) {
            Color::updateOrCreate(
                ['color' => $color['color']], 
                $color
            );
        }
    }
}
