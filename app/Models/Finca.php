<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Finca",
 *     type="object",
 *     title="Finca",
 *     description="Modelo de Finca",
 *     @OA\Property(property="id", type="integer", description="ID único de la finca", example=1),
 *     @OA\Property(property="nombre", type="string", maxLength=100, description="Nombre de la finca", example="Finca San José"),
 *     @OA\Property(property="estado", type="boolean", description="Estado de la finca", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Finca extends Model
{
    use HasFactory;
    
    protected $table = 'fincas';
    
    protected $fillable = [
        'nombre',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación muchos a muchos con Empleados
    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'empleado_finca', 'finca_id', 'empleado_id');
    }
    
    // Scope para obtener solo fincas activas
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
