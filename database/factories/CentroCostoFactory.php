<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TipoContrato;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentroCosto>
 */
class CentroCostoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grupos = ['Administrativo', 'Operacional', 'Comercial', 'Financiero', 'Logístico', 'Técnico'];
        
        return [
            'nombre' => $this->faker->unique()->company() . ' - Centro de Costo',
            'estado' => $this->faker->boolean(85), // 85% probabilidad de estar activo
            'grupo' => $this->faker->randomElement($grupos),
            'tipo_contrato_id' => TipoContrato::factory(),
        ];
    }
    
    /**
     * Indica que el centro de costo está activo
     */
    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => true,
        ]);
    }
    
    /**
     * Indica que el centro de costo está inactivo
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => false,
        ]);
    }
    
    /**
     * Asigna un grupo específico al centro de costo
     */
    public function conGrupo(string $grupo): static
    {
        return $this->state(fn (array $attributes) => [
            'grupo' => $grupo,
        ]);
    }
}
