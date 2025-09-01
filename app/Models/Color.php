<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Color",
 *     type="object",
 *     title="Color",
 *     description="Modelo de Color",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID único del color",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="color",
 *         type="string",
 *         maxLength=50,
 *         description="Nombre del color",
 *         example="Azul"
 *     ),
 *     @OA\Property(
 *         property="codigo",
 *         type="string",
 *         maxLength=7,
 *         description="Código hexadecimal del color",
 *         example="#0066CC"
 *     ),
 *     @OA\Property(
 *         property="estado",
 *         type="boolean",
 *         description="Estado del color (activo/inactivo)",
 *         example=true
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
class Color extends Model
{
    use HasFactory;
    
    protected $table = 'color';
    
    protected $fillable = [
        'color',
        'codigo',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relación uno a muchos con Departamentos (cada color puede ser usado por varios departamentos)
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'color_id');
    }
    
    // Scope para obtener solo colores activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
