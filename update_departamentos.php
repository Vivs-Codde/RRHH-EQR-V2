<?php

require_once 'vendor/autoload.php';

// Configurar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departamento;
use App\Models\Color;

echo "Actualizando departamentos con colores...\n";

// Obtener colores disponibles
$colores = Color::all();
echo "Colores disponibles:\n";
foreach($colores as $color) {
    echo $color->id . " - " . $color->color . " (" . $color->codigo . ")\n";
}

// Asignar colores a departamentos existentes
$departamentos = [
    'Administrativo' => 1, // Azul Administrativo
    'Cultivo' => 2,        // Verde Cultivo  
    'Postcosecha' => 3,    // Naranja Postcosecha
    'Bodega' => 4,         // Púrpura Supervisión
    'Cuarto Frio' => 3,    // Naranja Postcosecha
    'Taller' => 4          // Púrpura Supervisión
];

foreach($departamentos as $nombre => $colorId) {
    $dept = Departamento::where('nombre', $nombre)->first();
    if($dept) {
        $dept->update(['color_id' => $colorId]);
        echo "Actualizado: $nombre -> Color ID: $colorId\n";
    }
}

echo "¡Departamentos actualizados con colores!\n";
