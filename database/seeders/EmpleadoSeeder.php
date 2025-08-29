<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\User;
use App\Models\TipoContrato;
use App\Models\EstructuraOrganizacional;
use App\Models\Finca;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmpleadoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de empleados.
     */
    public function run(): void
    {
        // Asegurar que los datos relacionados existan
        $this->call(TipoContratoSeeder::class);
        $this->call(EstructuraOrganizacionalSeeder::class);
        $this->call(FincaSeeder::class);
        
        // Crear algunos usuarios para asociar con empleados si no existen
        $usuarios = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'María López',
                'email' => 'maria.lopez@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@example.com',
                'password' => Hash::make('password123'),
            ],
        ];
        
        $userIds = [];
        foreach ($usuarios as $datosUsuario) {
            $user = User::updateOrCreate(
                ['email' => $datosUsuario['email']],
                $datosUsuario
            );
            $userIds[] = $user->id;
        }
        
        // Obtener IDs de tipos de contrato, estructuras organizacionales y fincas
        $tipoContratoIds = TipoContrato::pluck('id')->toArray();
        $estructuraIds = EstructuraOrganizacional::pluck('id')->toArray();
        $fincaIds = Finca::pluck('id')->toArray();
        
        $empleados = [
            [
                'idempleado_As2' => 'EMP001',
                'estado_rrhh' => true,
                'estado_As2' => true,
                'nombre_As2' => 'Juan',
                'apellido_As2' => 'Pérez',
                'fechaNacimiento_As2' => '1990-05-15',
                'contacto' => '3001234567',
                'discapacidad_As2' => 'Ninguna',
                'porcentaje_discapacidad_As2' => 0,
                'fechaIngreso_As2' => '2021-01-10',
                'fechaIngreso_rrhh' => '2021-01-10',
                'estructuraCosto_As2' => 'CC001',
                'idSrv66' => 'SRV66001',
                'idSrv90' => 'SRV90001',
                'idAreas' => 'AREA001',
                'tipoUserBiometrico' => 'TIPO1',
                'foto_perfil' => null,
                'user_id' => $userIds[0],
                'tipo_contrato_id' => $tipoContratoIds[0],
                'estructura_organizacional_id' => $estructuraIds[0],
                'fincas' => [0, 1], // Índices de fincas a asignar
            ],
            [
                'idempleado_As2' => 'EMP002',
                'estado_rrhh' => true,
                'estado_As2' => true,
                'nombre_As2' => 'María',
                'apellido_As2' => 'López',
                'fechaNacimiento_As2' => '1988-09-22',
                'contacto' => '3009876543',
                'discapacidad_As2' => 'Ninguna',
                'porcentaje_discapacidad_As2' => 0,
                'fechaIngreso_As2' => '2020-05-20',
                'fechaIngreso_rrhh' => '2020-05-20',
                'estructuraCosto_As2' => 'CC002',
                'idSrv66' => 'SRV66002',
                'idSrv90' => 'SRV90002',
                'idAreas' => 'AREA002',
                'tipoUserBiometrico' => 'TIPO1',
                'foto_perfil' => null,
                'user_id' => $userIds[1],
                'tipo_contrato_id' => $tipoContratoIds[1],
                'estructura_organizacional_id' => $estructuraIds[1],
                'fincas' => [2], // Índice de fincas a asignar
            ],
            [
                'idempleado_As2' => 'EMP003',
                'estado_rrhh' => true,
                'estado_As2' => true,
                'nombre_As2' => 'Carlos',
                'apellido_As2' => 'Rodríguez',
                'fechaNacimiento_As2' => '1995-03-10',
                'contacto' => '3005551234',
                'discapacidad_As2' => 'Ninguna',
                'porcentaje_discapacidad_As2' => 0,
                'fechaIngreso_As2' => '2022-02-15',
                'fechaIngreso_rrhh' => '2022-02-15',
                'estructuraCosto_As2' => 'CC003',
                'idSrv66' => 'SRV66003',
                'idSrv90' => 'SRV90003',
                'idAreas' => 'AREA003',
                'tipoUserBiometrico' => 'TIPO1',
                'foto_perfil' => null,
                'user_id' => $userIds[2],
                'tipo_contrato_id' => $tipoContratoIds[2],
                'estructura_organizacional_id' => $estructuraIds[2],
                'fincas' => [3, 4], // Índices de fincas a asignar
            ],
        ];
        
        foreach ($empleados as $datosEmpleado) {
            $fincasAsignar = $datosEmpleado['fincas'];
            unset($datosEmpleado['fincas']);
            
            $empleado = Empleado::updateOrCreate(
                ['idempleado_As2' => $datosEmpleado['idempleado_As2']],
                $datosEmpleado
            );
            
            // Asignar fincas
            $fincasIds = [];
            foreach ($fincasAsignar as $indice) {
                if (isset($fincaIds[$indice])) {
                    $fincasIds[] = $fincaIds[$indice];
                }
            }
            
            if (!empty($fincasIds)) {
                $empleado->fincas()->sync($fincasIds);
            }
        }
    }
}
