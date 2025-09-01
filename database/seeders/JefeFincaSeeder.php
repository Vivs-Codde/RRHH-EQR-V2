<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstructuraOrganizacional;
use App\Models\Departamento;
use App\Models\Color;

class JefeFincaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener o crear el departamento Administrativo
        $departamentoAdministrativo = Departamento::where('nombre', 'Administrativo')->first();
        
        // Obtener departamentos de acceso
        $cultivo = Departamento::where('nombre', 'Cultivo')->first();
        $postcosecha = Departamento::where('nombre', 'Postcosecha')->first();
        $taller = Departamento::where('nombre', 'Taller')->first();
        
        // Verificar que todos los departamentos existen
        if (!$departamentoAdministrativo) {
            echo "Error: Departamento 'Administrativo' no encontrado\n";
            return;
        }
        
        if (!$cultivo) {
            echo "Error: Departamento 'Cultivo' no encontrado\n";
            return;
        }
        
        if (!$postcosecha) {
            echo "Error: Departamento 'Postcosecha' no encontrado\n";
            return;
        }
        
        if (!$taller) {
            echo "Error: Departamento 'Taller' no encontrado\n";
            return;
        }
        
        // Mostrar información de los departamentos y sus colores
        echo "=== INFORMACIÓN DE DEPARTAMENTOS ===\n";
        echo "Departamento principal: {$departamentoAdministrativo->nombre}\n";
        if ($departamentoAdministrativo->color) {
            echo "  - Color: {$departamentoAdministrativo->color->color} ({$departamentoAdministrativo->color->codigo})\n";
        }
        
        echo "\nDepartamentos de acceso:\n";
        echo "- Cultivo: ";
        if ($cultivo->color) {
            echo "{$cultivo->color->color} ({$cultivo->color->codigo})\n";
        } else {
            echo "Sin color asignado\n";
        }
        
        echo "- Postcosecha: ";
        if ($postcosecha->color) {
            echo "{$postcosecha->color->color} ({$postcosecha->color->codigo})\n";
        } else {
            echo "Sin color asignado\n";
        }
        
        echo "- Taller: ";
        if ($taller->color) {
            echo "{$taller->color->color} ({$taller->color->codigo})\n";
        } else {
            echo "Sin color asignado\n";
        }
        
        // Crear o actualizar la estructura organizacional
        $jefeFinca = EstructuraOrganizacional::firstOrCreate(
            ['cargo' => 'Jefe de Finca'],
            [
                'cargo' => 'Jefe de Finca',
                'departamento_id' => $departamentoAdministrativo->id,
                'estado' => true
            ]
        );
        
        // Asignar departamentos de acceso
        $departamentosAcceso = [
            $cultivo->id,
            $postcosecha->id,
            $taller->id
        ];
        
        $jefeFinca->departamentosAcceso()->sync($departamentosAcceso);
        
        echo "\n=== ESTRUCTURA CREADA ===\n";
        echo "Cargo: {$jefeFinca->cargo}\n";
        echo "Departamento principal: {$departamentoAdministrativo->nombre}\n";
        echo "Departamentos de acceso: " . count($departamentosAcceso) . " departamentos\n";
        
        // Mostrar los colores que aparecerán en el carnet
        $jefeFinca->load(['departamento.color', 'departamentosAcceso.color']);
        $coloresCarnet = $jefeFinca->departamentosAcceso->pluck('color')->filter()->unique('id');
        
        echo "\n=== COLORES PARA CARNET ===\n";
        foreach ($coloresCarnet as $color) {
            echo "- {$color->color} ({$color->codigo})\n";
        }
        
        echo "\n¡Seeder completado exitosamente!\n";
    }
}
