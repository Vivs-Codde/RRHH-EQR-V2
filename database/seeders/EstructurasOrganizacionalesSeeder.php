<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstructuraOrganizacional;
use App\Models\Departamento;

class EstructurasOrganizacionalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener departamentos
        $administrativo = Departamento::where('nombre', 'Administrativo')->first();
        $cultivo = Departamento::where('nombre', 'Cultivo')->first();
        $postcosecha = Departamento::where('nombre', 'Postcosecha')->first();
        $bodega = Departamento::where('nombre', 'Bodega')->first();

        // Crear estructuras organizacionales de ejemplo
        $estructuras = [
            [
                'cargo' => 'Gerente General',
                'departamento_id' => $administrativo->id,
                'departamentos_acceso' => [$administrativo->id, $cultivo->id, $postcosecha->id, $bodega->id], // Acceso a todos
            ],
            [
                'cargo' => 'Supervisor de Cultivo',
                'departamento_id' => $cultivo->id,
                'departamentos_acceso' => [$cultivo->id, $bodega->id], // Acceso a cultivo y bodega
            ],
            [
                'cargo' => 'Operario de Postcosecha',
                'departamento_id' => $postcosecha->id,
                'departamentos_acceso' => [$postcosecha->id], // Solo acceso a postcosecha
            ],
            [
                'cargo' => 'Contador',
                'departamento_id' => $administrativo->id,
                'departamentos_acceso' => [$administrativo->id], // Solo acceso administrativo
            ],
            [
                'cargo' => 'Jefe de Bodega',
                'departamento_id' => $bodega->id,
                'departamentos_acceso' => [$bodega->id, $postcosecha->id], // Acceso a bodega y postcosecha
            ],
        ];

        foreach ($estructuras as $estructuraData) {
            $departamentosAcceso = $estructuraData['departamentos_acceso'];
            unset($estructuraData['departamentos_acceso']);

            $estructura = EstructuraOrganizacional::firstOrCreate(
                ['cargo' => $estructuraData['cargo']],
                $estructuraData
            );

            // Asignar departamentos de acceso
            $estructura->departamentosAcceso()->sync($departamentosAcceso);

            echo "Creada estructura: {$estructura->cargo} con acceso a " . count($departamentosAcceso) . " departamentos\n";
        }
    }
}
