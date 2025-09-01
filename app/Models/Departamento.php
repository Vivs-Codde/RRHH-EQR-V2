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
 *     @OA\Property(property="color_id", type="integer", description="ID del color característico del departamento", example=1),
 *     @OA\Property(property="estado", type="boolean", description="Estado del departamento", example=true),
 *     @OA\Property(property="color", type="object", ref="#/components/schemas/Color", description="Color característico del departamento"),
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
        'color_id',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con Color (cada departamento tiene un color característico)
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    // Relación uno a muchos con EstructuraOrganizacional (cargos que pertenecen al departamento)
    public function estructurasOrganizacionales()
    {
        return $this->hasMany(EstructuraOrganizacional::class, 'departamento_id');
    }

    // Relación muchos a muchos con EstructuraOrganizacional a través de la tabla intermedia
    // (departamentos a los que tienen acceso los cargos)
    public function estructurasConAcceso()
    {
        return $this->belongsToMany(
            EstructuraOrganizacional::class,
            'estructura_organizacional_color',
            'departamento_id',
            'estructura_organizacional_id'
        )->withTimestamps();
    }
    
    // Scope para obtener solo departamentos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
