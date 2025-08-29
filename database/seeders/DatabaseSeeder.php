<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        if (class_exists(RoleAndPermissionSeeder::class)) {
            $this->call(RoleAndPermissionSeeder::class);
        }
        
        // Ejecutar nuestro nuevo seeder de permisos con guard sanctum
        $this->call(PermissionSeeder::class);
    }
}
