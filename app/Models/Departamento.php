<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Departamento",
 *     type="object",
 *     title="Departamento",
 *     description="Modelo de Departamento",
 *     @OA\Property(property="id", type="integer", description="ID único del departamento", example=1),
 *     @OA\Property(property="nombre", type="string", maxLength=100, description="Nombre del departamento", example="Recursos Humanos"),
 *     @OA\Property(property="color", type="string", maxLength=7, description="Código de color hexadecimal", example="#FF5733"),
 *     @OA\Property(property="estado", type="boolean", description="Estado del departamento", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Departamento extends Model
{
    use HasFactory;
    
    protected $table = 'departamentos';
    
    protected $fillable = [
        'nombre',
        'color',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación uno a muchos con EstructuraOrganizacional
    public function estructurasOrganizacionales()
    {
        return $this->hasMany(EstructuraOrganizacional::class, 'departamento_id');
    }
    
    // Scope para obtener solo departamentos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
