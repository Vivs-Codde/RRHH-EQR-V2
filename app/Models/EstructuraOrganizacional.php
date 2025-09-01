<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="EstructuraOrganizacional",
 *     type="object",
 *     title="Estructura Organizacional",
 *     description="Modelo de Estructura Organizacional",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID único de la estructura organizacional",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="cargo",
 *         type="string",
 *         maxLength=150,
 *         description="Nombre del cargo",
 *         example="Gerente de Recursos Humanos"
 *     ),
 *     @OA\Property(
 *         property="departamento_id",
 *         type="integer",
 *         description="ID del departamento al que pertenece",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="estado",
 *         type="boolean",
 *         description="Estado de la estructura organizacional (activo/inactivo)",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="departamento",
 *         type="object",
 *         description="Departamento al que pertenece el cargo"
 *     ),
 *     @OA\Property(
 *         property="departamentos_acceso",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Departamento"),
 *         description="Departamentos a los que tiene acceso el cargo"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de creación",
 *         example="2025-08-27T16:42:23.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de última actualización",
 *         example="2025-08-27T16:42:23.000000Z"
 *     )
 * )
 */
class EstructuraOrganizacional extends Model
{
    use HasFactory;
    
    protected $table = 'estructura_organizacional';
    
    protected $fillable = [
        'cargo',
        'departamento_id',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relación con Departamento al que pertenece el cargo (muchos a uno)
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
    
    // Relación muchos a muchos con Departamentos a los que tiene acceso el cargo
    public function departamentosAcceso()
    {
        return $this->belongsToMany(
            Departamento::class,
            'estructura_organizacional_color',
            'estructura_organizacional_id',
            'departamento_id'
        )->withTimestamps();
    }
    
    // Método para obtener los colores de los departamentos a los que tiene acceso
    public function coloresAcceso()
    {
        return $this->departamentosAcceso()->with('color');
    }
    
    // Relación con Empleados (uno a muchos)
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'estructura_organizacional_id');
    }
    
    // Scope para obtener solo estructuras activas
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
    
    // Scope para incluir relaciones comunes
    public function scopeConRelaciones($query)
    {
        return $query->with(['departamento', 'departamentosAcceso.color']);
    }
}
