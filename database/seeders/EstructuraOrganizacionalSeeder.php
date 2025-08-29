<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\EstructuraOrganizacional;
use App\Models\Color;
use Illuminate\Database\Seeder;

class EstructuraOrganizacionalSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de estructura organizacional.
     */
    public function run(): void
    {
        // Asegurar que los departamentos existan
        $this->call(DepartamentoSeeder::class);
        $this->call(ColorSeeder::class);
        
        $estructuras = [
            [
                'cargo' => 'Gerente de Recursos Humanos',
                'departamento' => 'Recursos Humanos',
                'colores' => ['Azul', 'Rojo'],
                'estado' => true,
            ],
            [
                'cargo' => 'Analista de Contabilidad',
                'departamento' => 'Contabilidad',
                'colores' => ['Verde', 'Amarillo'],
                'estado' => true,
            ],
            [
                'cargo' => 'Director de Ventas',
                'departamento' => 'Ventas',
                'colores' => ['Naranja'],
                'estado' => true,
            ],
            [
                'cargo' => 'Desarrollador de Software',
                'departamento' => 'Tecnología',
                'colores' => ['Morado', 'Azul'],
                'estado' => true,
            ],
            [
                'cargo' => 'Coordinador de Operaciones',
                'departamento' => 'Operaciones',
                'colores' => ['Verde'],
                'estado' => true,
            ],
        ];

        foreach ($estructuras as $estructura) {
            // Buscar ID del departamento
            $departamento = Departamento::where('nombre', $estructura['departamento'])->first();
            
            if ($departamento) {
                // Crear la estructura organizacional
                $nuevaEstructura = EstructuraOrganizacional::updateOrCreate(
                    [
                        'cargo' => $estructura['cargo'],
                        'departamento_id' => $departamento->id
                    ], 
                    [
                        'cargo' => $estructura['cargo'],
                        'departamento_id' => $departamento->id,
                        'estado' => $estructura['estado']
                    ]
                );
                
                // Asignar colores
                if (isset($estructura['colores']) && is_array($estructura['colores'])) {
                    $coloresIds = [];
                    foreach ($estructura['colores'] as $nombreColor) {
                        $color = Color::where('color', $nombreColor)->first();
                        if ($color) {
                            $coloresIds[] = $color->id;
                        }
                    }
                    
                    if (!empty($coloresIds)) {
                        $nuevaEstructura->colores()->sync($coloresIds);
                    }
                }
            }
        }
    }
}
