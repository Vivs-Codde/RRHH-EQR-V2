<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que tenemos los permisos básicos
        $basicPermissions = [
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'ver empleados',
            'crear empleados',
            'editar empleados',
            'eliminar empleados',
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles'
        ];

        // Crear los permisos con guard "sanctum"
        foreach ($basicPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Asegurarse de que existe el rol "admin" con guard "sanctum"
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        
        // Asignar todos los permisos al rol admin
        $adminRole->syncPermissions(Permission::where('guard_name', 'sanctum')->get());
    }
}
