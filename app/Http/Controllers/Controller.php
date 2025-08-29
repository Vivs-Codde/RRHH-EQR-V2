<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="RRHH API",
 *     version="1.0.0",
 *     description="API para el sistema de gestión de Recursos Humanos",
 *     @OA\Contact(
 *         email="admin@empresa.com"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Ingrese el token de autenticación Bearer"
 * )
 * 
 * @OA\Schema(
 *     schema="RolePermissions",
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         description="Roles asignados al usuario",
 *         @OA\Items(type="string", example="admin")
 *     ),
 *     @OA\Property(
 *         property="permissions",
 *         type="array",
 *         description="Permisos asignados al usuario",
 *         @OA\Items(type="string", example="crear usuarios")
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor de desarrollo local"
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor local alternativo"
 * )
 */
abstract class Controller
{
    //
}
