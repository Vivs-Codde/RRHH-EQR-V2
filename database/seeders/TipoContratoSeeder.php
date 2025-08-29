<?php

namespace Database\Seeders;

use App\Models\TipoContrato;
use Illuminate\Database\Seeder;

class TipoContratoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de tipos de contrato.
     */
    public function run(): void
    {
        $tiposContrato = [
            [
                'tipo' => 'Indefinido',
                'estado' => true,
            ],
            [
                'tipo' => 'Temporal',
                'estado' => true,
            ],
            [
                'tipo' => 'Por Obra',
                'estado' => true,
            ],
            [
                'tipo' => 'Medio Tiempo',
                'estado' => true,
            ],
            [
                'tipo' => 'Prácticas',
                'estado' => true,
            ],
            [
                'tipo' => 'Servicios Profesionales',
                'estado' => true,
            ],
        ];

        foreach ($tiposContrato as $tipo) {
            TipoContrato::updateOrCreate(
                ['tipo' => $tipo['tipo']], 
                $tipo
            );
        }
    }
}
