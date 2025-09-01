<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colores = [
            [
                'color' => 'Azul Administrativo',
                'codigo' => '#2563EB',
                'estado' => true,
            ],
            [
                'color' => 'Verde Cultivo',
                'codigo' => '#16A34A',
                'estado' => true,
            ],
            [
                'color' => 'Naranja Postcosecha',
                'codigo' => '#EA580C',
                'estado' => true,
            ],
            [
                'color' => 'Púrpura Supervisión',
                'codigo' => '#9333EA',
                'estado' => true,
            ],
            [
                'color' => 'Rojo Gerencial',
                'codigo' => '#DC2626',
                'estado' => true,
            ],
        ];

        foreach ($colores as $color) {
            Color::firstOrCreate(
                ['codigo' => $color['codigo']],
                $color
            );
        }
    }
}
