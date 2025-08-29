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

        // Crear permisos para usuarios
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'ver usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);
        
        // Crear permisos para empleados
        Permission::create(['name' => 'crear empleados']);
        Permission::create(['name' => 'ver empleados']);
        Permission::create(['name' => 'editar empleados']);
        Permission::create(['name' => 'eliminar empleados']);
        
        // Crear permisos para departamentos
        Permission::create(['name' => 'crear departamentos']);
        Permission::create(['name' => 'ver departamentos']);
        Permission::create(['name' => 'editar departamentos']);
        Permission::create(['name' => 'eliminar departamentos']);
        
        // Crear permisos para estructuras organizacionales
        Permission::create(['name' => 'crear estructuras']);
        Permission::create(['name' => 'ver estructuras']);
        Permission::create(['name' => 'editar estructuras']);
        Permission::create(['name' => 'eliminar estructuras']);
        
        // Crear permisos para fincas
        Permission::create(['name' => 'crear fincas']);
        Permission::create(['name' => 'ver fincas']);
        Permission::create(['name' => 'editar fincas']);
        Permission::create(['name' => 'eliminar fincas']);

        // Crear permisos para colores
        Permission::create(['name' => 'crear colores']);
        Permission::create(['name' => 'ver colores']);
        Permission::create(['name' => 'editar colores']);
        Permission::create(['name' => 'eliminar colores']);

        // Crear permisos para tipos de contrato
        Permission::create(['name' => 'crear tipos-contrato']);
        Permission::create(['name' => 'ver tipos-contrato']);
        Permission::create(['name' => 'editar tipos-contrato']);
        Permission::create(['name' => 'eliminar tipos-contrato']);

        // Crear roles y asignar permisos
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());
        
        $roleRRHH = Role::create(['name' => 'rrhh']);
        $roleRRHH->givePermissionTo([
            'ver usuarios', 'crear empleados', 'ver empleados', 'editar empleados',
            'ver departamentos', 'ver estructuras', 'ver fincas', 'ver tipos-contrato',
            'ver colores'
        ]);
        
        $roleGerente = Role::create(['name' => 'gerente']);
        $roleGerente->givePermissionTo([
            'ver empleados', 'ver departamentos', 'ver estructuras', 
            'ver fincas', 'ver tipos-contrato'
        ]);
        
        $roleVisor = Role::create(['name' => 'visor']);
        $roleVisor->givePermissionTo([
            'ver empleados', 'ver departamentos', 'ver estructuras'
        ]);

        // Crear un usuario admin y asignarle el rol
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => bcrypt('password')
            ]);
        }
        $admin->assignRole('admin');
    }
}
