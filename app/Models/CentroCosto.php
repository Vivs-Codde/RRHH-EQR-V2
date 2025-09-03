<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="CentroCosto",
 *     type="object",
 *     title="Centro de Costo",
 *     description="Modelo de Centro de Costo",
 *     @OA\Property(property="id", type="integer", description="ID único del centro de costo", example=1),
 *     @OA\Property(property="nombre", type="string", maxLength=150, description="Nombre del centro de costo", example="Administración"),
 *     @OA\Property(property="estado", type="boolean", description="Estado del centro de costo", example=true),
 *     @OA\Property(property="grupo", type="string", maxLength=100, description="Grupo al que pertenece el centro de costo", example="Operacional"),
 *     @OA\Property(property="tipo_contrato_id", type="integer", description="ID del tipo de contrato relacionado", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class CentroCosto extends Model
{
    use HasFactory;
    
    protected $table = 'centro_costos';
    
    protected $fillable = [
        'nombre',
        'estado',
        'grupo',
        'tipo_contrato_id',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación muchos a uno con TipoContrato
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }
    
    // Scope para obtener solo centros de costo activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
    
    // Scope para filtrar por grupo
    public function scopePorGrupo($query, $grupo)
    {
        return $query->where('grupo', $grupo);
    }
}
