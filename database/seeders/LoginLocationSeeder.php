<?php

namespace Database\Seeders;

use App\Models\LoginLocation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoginLocationSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para la tabla de ubicaciones de inicio de sesión.
     */
    public function run(): void
    {
        $usuarios = User::all();
        
        foreach ($usuarios as $usuario) {
            // Crear algunas ubicaciones de inicio de sesión para cada usuario
            $loginLocations = [
                [
                    'user_id' => $usuario->id,
                    'latitude' => 3.4516467, // Colombia, ejemplo
                    'longitude' => -76.5319854,
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36',
                    'login_at' => Carbon::now()->subDays(rand(1, 30))
                ],
                [
                    'user_id' => $usuario->id,
                    'latitude' => 3.4372201, // Otro ejemplo en Colombia
                    'longitude' => -76.5224991,
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.1 Safari/605.1.15',
                    'login_at' => Carbon::now()->subDays(rand(1, 15))
                ],
                [
                    'user_id' => $usuario->id,
                    'latitude' => 3.4234501, // Otro más
                    'longitude' => -76.5401234,
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                    'login_at' => Carbon::now()->subDays(rand(1, 7))
                ],
            ];
            
            foreach ($loginLocations as $location) {
                LoginLocation::create($location);
            }
        }
    }
}
