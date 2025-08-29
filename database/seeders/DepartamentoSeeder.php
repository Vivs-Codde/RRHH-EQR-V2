<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de departamentos.
     */
    public function run(): void
    {
        $departamentos = [
            [
                'nombre' => 'Recursos Humanos',
                'color' => '#FF5733',
                'estado' => true,
            ],
            [
                'nombre' => 'Contabilidad',
                'color' => '#33FF57',
                'estado' => true,
            ],
            [
                'nombre' => 'Ventas',
                'color' => '#3357FF',
                'estado' => true,
            ],
            [
                'nombre' => 'Tecnología',
                'color' => '#F033FF',
                'estado' => true,
            ],
            [
                'nombre' => 'Operaciones',
                'color' => '#FF33A8',
                'estado' => true,
            ],
        ];

        foreach ($departamentos as $departamento) {
            Departamento::updateOrCreate(
                ['nombre' => $departamento['nombre']], 
                $departamento
            );
        }
    }
}
