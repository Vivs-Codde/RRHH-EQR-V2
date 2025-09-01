<?php

require_once 'vendor/autoload.php';

// Configurar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EstructuraOrganizacional;

$jefeFinca = EstructuraOrganizacional::where('cargo', 'Jefe de Finca')->first();
echo "ID del Jefe de Finca: " . $jefeFinca->id . "\n";

echo "Puedes probar el endpoint:\n";
echo "GET /api/estructuras-organizacionales/{$jefeFinca->id}/colores-carnet\n";
