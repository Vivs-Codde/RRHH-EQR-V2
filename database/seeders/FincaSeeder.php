<?php

namespace Database\Seeders;

use App\Models\Finca;
use Illuminate\Database\Seeder;

class FincaSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de fincas.
     */
    public function run(): void
    {
        $fincas = [
            [
                'nombre' => 'Finca San José',
                'estado' => true,
            ],
            [
                'nombre' => 'Finca El Paraíso',
                'estado' => true,
            ],
            [
                'nombre' => 'Finca La Esperanza',
                'estado' => true,
            ],
            [
                'nombre' => 'Finca Santa Rosa',
                'estado' => true,
            ],
            [
                'nombre' => 'Finca Buenos Aires',
                'estado' => true,
            ],
        ];

        foreach ($fincas as $finca) {
            Finca::updateOrCreate(
                ['nombre' => $finca['nombre']], 
                $finca
            );
        }
    }
}
