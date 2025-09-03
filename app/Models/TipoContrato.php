<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="TipoContrato",
 *     type="object",
 *     title="Tipo Contrato",
 *     description="Modelo de Tipo de Contrato",
 *     @OA\Property(property="id", type="integer", description="ID único del tipo de contrato", example=1),
 *     @OA\Property(property="tipo", type="string", maxLength=100, description="Nombre del tipo de contrato", example="Indefinido"),
 *     @OA\Property(property="estado", type="boolean", description="Estado del tipo de contrato", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class TipoContrato extends Model
{
    use HasFactory;
    
    protected $table = 'tipo_contrato';
    
    protected $fillable = [
        'tipo',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación uno a muchos con Empleados
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'tipo_contrato_id');
    }
    
    // Relación uno a muchos con CentrosCosto
    public function centrosCosto()
    {
        return $this->hasMany(CentroCosto::class, 'tipo_contrato_id');
    }
    
    // Scope para obtener solo tipos de contrato activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
