<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Empleado",
 *     type="object",
 *     title="Empleado",
 *     description="Modelo de Empleado",
 *     @OA\Property(property="id", type="integer", description="ID único del empleado", example=1),
 *     @OA\Property(property="idempleado_As2", type="string", maxLength=10, description="ID del empleado en AS2", example="EMP001"),
 *     @OA\Property(property="estado_rrhh", type="boolean", description="Estado RRHH", example=true),
 *     @OA\Property(property="estado_As2", type="boolean", description="Estado AS2", example=true),
 *     @OA\Property(property="nombre_As2", type="string", maxLength=20, description="Nombre", example="Juan"),
 *     @OA\Property(property="apellido_As2", type="string", maxLength=20, description="Apellido", example="Pérez"),
 *     @OA\Property(property="fechaNacimiento_As2", type="string", format="date", description="Fecha de nacimiento", example="1990-01-15"),
 *     @OA\Property(property="contacto", type="string", maxLength=10, description="Contacto", example="3001234567"),
 *     @OA\Property(property="discapacidad_As2", type="string", maxLength=20, description="Discapacidad", example="Ninguna"),
 *     @OA\Property(property="porcentaje_discapacidad_As2", type="integer", description="Porcentaje de discapacidad", example=0),
 *     @OA\Property(property="fechaIngreso_As2", type="string", format="date", description="Fecha de ingreso AS2", example="2020-03-01"),
 *     @OA\Property(property="fechaSalida_As2", type="string", format="date", description="Fecha de salida AS2", example=null),
 *     @OA\Property(property="estructuraCosto_As2", type="string", description="Estructura de costo AS2", example="CC001"),
 *     @OA\Property(property="fechaIngreso_rrhh", type="string", format="date", description="Fecha de ingreso RRHH", example="2020-03-01"),
 *     @OA\Property(property="fechaSalida_rrhh", type="string", format="date", description="Fecha de salida RRHH", example=null),
 *     @OA\Property(property="idSrv66", type="string", description="ID Srv66", example="SRV66001"),
 *     @OA\Property(property="idSrv90", type="string", description="ID Srv90", example="SRV90001"),
 *     @OA\Property(property="idAreas", type="string", description="ID Areas", example="AREA001"),
 *     @OA\Property(property="tipoUserBiometrico", type="string", description="Tipo usuario biométrico", example="TIPO1"),
 *     @OA\Property(property="foto_perfil", type="string", description="URL foto de perfil", example="profile.jpg"),
 *     @OA\Property(property="user_id", type="integer", description="ID del usuario asociado", example=1),
 *     @OA\Property(property="tipo_contrato_id", type="integer", description="ID del tipo de contrato", example=1),
 *     @OA\Property(property="estructura_organizacional_id", type="integer", description="ID de la estructura organizacional", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Empleado extends Model
{
    use HasFactory;
    
    protected $table = 'empleados';
    
    protected $fillable = [
        'idempleado_As2',
        'estado_rrhh',
        'estado_As2',
        'nombre_As2',
        'apellido_As2',
        'fechaNacimiento_As2',
        'contacto',
        'discapacidad_As2',
        'porcentaje_discapacidad_As2',
        'fechaIngreso_As2',
        'fechaSalida_As2',
        'estructuraCosto_As2',
        'fechaIngreso_rrhh',
        'fechaSalida_rrhh',
        'idSrv66',
        'idSrv90',
        'idAreas',
        'tipoUserBiometrico',
        'foto_perfil',
        'user_id',
        'tipo_contrato_id',
        'estructura_organizacional_id',
    ];
    
    protected $casts = [
        'estado_rrhh' => 'boolean',
        'estado_As2' => 'boolean',
        'fechaNacimiento_As2' => 'date',
        'fechaIngreso_As2' => 'date',
        'fechaSalida_As2' => 'date',
        'fechaIngreso_rrhh' => 'date',
        'fechaSalida_rrhh' => 'date',
        'porcentaje_discapacidad_As2' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación uno a uno con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relación muchos a uno con TipoContrato
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }
    
    // Relación muchos a uno con EstructuraOrganizacional
    public function estructuraOrganizacional()
    {
        return $this->belongsTo(EstructuraOrganizacional::class, 'estructura_organizacional_id');
    }
    
    // Relación muchos a muchos con Fincas
    public function fincas()
    {
        return $this->belongsToMany(Finca::class, 'empleado_finca', 'empleado_id', 'finca_id');
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado_rrhh', true)->where('estado_As2', true);
    }
    
    public function scopeConRelaciones($query)
    {
        return $query->with(['user', 'tipoContrato', 'estructuraOrganizacional', 'fincas']);
    }
    
    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre_As2 . ' ' . $this->apellido_As2;
    }
}
