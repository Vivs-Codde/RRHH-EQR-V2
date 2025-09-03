<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CentroCosto;
use App\Models\TipoContrato;

class CentroCostoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos tipos de contrato existentes
        $tiposContrato = TipoContrato::all();
        
        if ($tiposContrato->isEmpty()) {
            $this->command->error('No existen tipos de contrato. Por favor, ejecuta el seeder de TipoContrato primero.');
            return;
        }
        
        $centrosCosto = [
            [
                'nombre' => 'Administración General',
                'estado' => true,
                'grupo' => 'Administrativo',
                'tipo_contrato_id' => $tiposContrato->first()->id,
            ],
            [
                'nombre' => 'Recursos Humanos',
                'estado' => true,
                'grupo' => 'Administrativo',
                'tipo_contrato_id' => $tiposContrato->first()->id,
            ],
            [
                'nombre' => 'Producción Agrícola',
                'estado' => true,
                'grupo' => 'Operacional',
                'tipo_contrato_id' => $tiposContrato->count() > 1 ? $tiposContrato->skip(1)->first()->id : $tiposContrato->first()->id,
            ],
            [
                'nombre' => 'Mantenimiento',
                'estado' => true,
                'grupo' => 'Operacional',
                'tipo_contrato_id' => $tiposContrato->count() > 1 ? $tiposContrato->skip(1)->first()->id : $tiposContrato->first()->id,
            ],
            [
                'nombre' => 'Ventas y Marketing',
                'estado' => true,
                'grupo' => 'Comercial',
                'tipo_contrato_id' => $tiposContrato->first()->id,
            ],
        ];
        
        foreach ($centrosCosto as $centroCosto) {
            CentroCosto::firstOrCreate(
                ['nombre' => $centroCosto['nombre']],
                $centroCosto
            );
        }
        
        $this->command->info('Centros de Costo creados exitosamente.');
    }
}
