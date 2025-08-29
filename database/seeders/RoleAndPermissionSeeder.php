<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos para usuarios con guardia sanctum explícita
        Permission::create(['name' => 'crear usuarios', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver usuarios', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar usuarios', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar usuarios', 'guard_name' => 'sanctum']);
        
        // Crear permisos para empleados con guardia sanctum explícita
        Permission::create(['name' => 'crear empleados', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver empleados', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar empleados', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar empleados', 'guard_name' => 'sanctum']);
        
        // Crear permisos para departamentos con guardia sanctum explícita
        Permission::create(['name' => 'crear departamentos', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver departamentos', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar departamentos', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar departamentos', 'guard_name' => 'sanctum']);
        
        // Crear permisos para estructuras organizacionales con guardia sanctum explícita
        Permission::create(['name' => 'crear estructuras', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver estructuras', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar estructuras', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar estructuras', 'guard_name' => 'sanctum']);
        
        // Crear permisos para fincas con guardia sanctum explícita
        Permission::create(['name' => 'crear fincas', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver fincas', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar fincas', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar fincas', 'guard_name' => 'sanctum']);

        // Crear permisos para colores con guardia sanctum explícita
        Permission::create(['name' => 'crear colores', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver colores', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar colores', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar colores', 'guard_name' => 'sanctum']);

        // Crear permisos para tipos de contrato con guardia sanctum explícita
        Permission::create(['name' => 'crear tipos-contrato', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'ver tipos-contrato', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'editar tipos-contrato', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'eliminar tipos-contrato', 'guard_name' => 'sanctum']);

        // Crear roles y asignar permisos con guardia sanctum explícita
        $roleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
        $roleAdmin->givePermissionTo(Permission::where('guard_name', 'sanctum')->get());
        
        $roleRRHH = Role::create(['name' => 'rrhh', 'guard_name' => 'sanctum']);
        $permissionsRRHH = Permission::where('guard_name', 'sanctum')
            ->whereIn('name', [
                'ver usuarios', 'crear empleados', 'ver empleados', 'editar empleados',
                'ver departamentos', 'ver estructuras', 'ver fincas', 'ver tipos-contrato',
                'ver colores'
            ])->get();
        $roleRRHH->givePermissionTo($permissionsRRHH);
        
        $roleGerente = Role::create(['name' => 'gerente', 'guard_name' => 'sanctum']);
        $permissionsGerente = Permission::where('guard_name', 'sanctum')
            ->whereIn('name', [
                'ver empleados', 'ver departamentos', 'ver estructuras', 
                'ver fincas', 'ver tipos-contrato'
            ])->get();
        $roleGerente->givePermissionTo($permissionsGerente);
        
        $roleVisor = Role::create(['name' => 'visor', 'guard_name' => 'sanctum']);
        $permissionsVisor = Permission::where('guard_name', 'sanctum')
            ->whereIn('name', [
                'ver empleados', 'ver departamentos', 'ver estructuras'
            ])->get();
        $roleVisor->givePermissionTo($permissionsVisor);

        // Crear un usuario admin y asignarle el rol con guardia sanctum
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => bcrypt('password')
            ]);
        }
        
        // Asignar rol usando el ID en lugar del objeto
        $admin->assignRole($roleAdmin->id);
    }
}
