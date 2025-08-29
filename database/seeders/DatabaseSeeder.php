<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\DepartamentoSeeder;
use Database\Seeders\FincaSeeder;
use Database\Seeders\TipoContratoSeeder;
use Database\Seeders\EstructuraOrganizacionalSeeder;
use Database\Seeders\EmpleadoSeeder;
use Database\Seeders\LoginLocationSeeder;
use Database\Seeders\RoleAndPermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Ejecutar el seeder de roles y permisos
        $this->call(RoleAndPermissionSeeder::class);
        
        // Ejecutar seeders para los modelos de la aplicación
        $this->call([
            ColorSeeder::class,
            DepartamentoSeeder::class,
            FincaSeeder::class,
            TipoContratoSeeder::class,
            EstructuraOrganizacionalSeeder::class,
            EmpleadoSeeder::class,
            LoginLocationSeeder::class,
        ]);
    }
}
