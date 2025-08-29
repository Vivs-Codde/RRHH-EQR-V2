<?php

// Script para agregar security a todas las rutas de Swagger
$controllers = [
    'app\Http\Controllers\Api\FincaController.php',
    'app\Http\Controllers\Api\EmpleadoController.php',
    'app\Http\Controllers\Api\EstructuraOrganizacionalController.php',
    'app\Http\Controllers\Api\ColorController.php'
];

foreach ($controllers as $controller) {
    $filePath = __DIR__ . '/' . $controller;
    
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Patrones para encontrar las anotaciones OA sin security
        $patterns = [
            '/(\s+\*\s+@OA\\Get\(\s*\n\s+\*\s+path="[^"]+",\s*\n\s+\*\s+summary="[^"]+",\s*\n\s+\*\s+description="[^"]+",\s*\n\s+\*\s+tags=\{[^}]+\})(?!\s*,\s*\n\s+\*\s+security=)/m',
            '/(\s+\*\s+@OA\\Post\(\s*\n\s+\*\s+path="[^"]+",\s*\n\s+\*\s+summary="[^"]+",\s*\n\s+\*\s+description="[^"]+",\s*\n\s+\*\s+tags=\{[^}]+\})(?!\s*,\s*\n\s+\*\s+security=)/m',
            '/(\s+\*\s+@OA\\Put\(\s*\n\s+\*\s+path="[^"]+",\s*\n\s+\*\s+summary="[^"]+",\s*\n\s+\*\s+description="[^"]+",\s*\n\s+\*\s+tags=\{[^}]+\})(?!\s*,\s*\n\s+\*\s+security=)/m',
            '/(\s+\*\s+@OA\\Delete\(\s*\n\s+\*\s+path="[^"]+",\s*\n\s+\*\s+summary="[^"]+",\s*\n\s+\*\s+description="[^"]+",\s*\n\s+\*\s+tags=\{[^}]+\})(?!\s*,\s*\n\s+\*\s+security=)/m'
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '$1,' . "\n" . '     *     security={{"bearerAuth": {}}},', $content);
        }
        
        file_put_contents($filePath, $content);
        echo "Actualizado: $controller\n";
    }
}

echo "Script completado!\n";
?>
