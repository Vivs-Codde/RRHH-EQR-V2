<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Color;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los colores creados
        $colorAdministrativo = Color::where('color', 'Azul Administrativo')->first();
        $colorCultivo = Color::where('color', 'Verde Cultivo')->first();
        $colorPostcosecha = Color::where('color', 'Naranja Postcosecha')->first();

        $departamentos = [
            [
                'nombre' => 'Administrativo',
                'color_id' => $colorAdministrativo->id,
                'estado' => true,
            ],
            [
                'nombre' => 'Cultivo',
                'color_id' => $colorCultivo->id,
                'estado' => true,
            ],
            [
                'nombre' => 'Postcosecha',
                'color_id' => $colorPostcosecha->id,
                'estado' => true,
            ],
        ];

        foreach ($departamentos as $departamento) {
            Departamento::firstOrCreate(
                ['nombre' => $departamento['nombre']],
                $departamento
            );
        }
    }
}
