<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador si no existe
        $admin = User::where('email', 'admin@test.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@test.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Usuario administrador creado:');
            $this->command->info('Email: admin@test.com');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('El usuario administrador ya existe.');
        }
    }
}
