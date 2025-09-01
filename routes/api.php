<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\EstructuraOrganizacionalController;
use App\Http\Controllers\Api\EmpleadoController;
use App\Http\Controllers\Api\TipoContratoController;
use App\Http\Controllers\Api\FincaController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\RolePermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación (públicas)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Rutas para administración de usuarios y roles
    // Quitamos el middleware de role para evitar el error
    // y lo manejaremos manualmente en el controlador
    
    // Ruta específica para crear empleados
    Route::post('/users/create-employee', [UserController::class, 'createEmployee']);
    // Rutas de API para usuarios (excepto crear/registrar)
    Route::apiResource('users', UserController::class)->except(['store']);
    
    // Rutas para administración de roles y permisos
    Route::get('/roles', [RolePermissionController::class, 'listRoles']);
    Route::post('/roles', [RolePermissionController::class, 'createRole']);
    Route::put('/roles/{id}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/roles/{id}', [RolePermissionController::class, 'deleteRole']);
    Route::get('/permissions', [RolePermissionController::class, 'listPermissions']);
    Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
    Route::post('/assign-role', [RolePermissionController::class, 'assignRole']);
    Route::get('/users-with-roles', [RolePermissionController::class, 'getUsersWithRoles']);
    
    // Rutas para colores
    Route::get('colores', [ColorController::class, 'index']);
    Route::get('colores/{id}', [ColorController::class, 'show']);
    Route::post('colores', [ColorController::class, 'store']);
    Route::put('colores/{id}', [ColorController::class, 'update']);
    Route::delete('colores/{id}', [ColorController::class, 'destroy']);
    
    // Rutas para estructuras organizacionales
    Route::get('estructuras-organizacionales', [EstructuraOrganizacionalController::class, 'index']);
    Route::get('estructuras-organizacionales/{id}', [EstructuraOrganizacionalController::class, 'show']);
    Route::post('estructuras-organizacionales', [EstructuraOrganizacionalController::class, 'store']);
    Route::put('estructuras-organizacionales/{id}', [EstructuraOrganizacionalController::class, 'update']);
    Route::delete('estructuras-organizacionales/{id}', [EstructuraOrganizacionalController::class, 'destroy']);
    Route::post('estructuras-organizacionales/{id}/departamentos-acceso', [EstructuraOrganizacionalController::class, 'asignarDepartamentosAcceso']);
    Route::get('estructuras-organizacionales/{id}/colores-carnet', [EstructuraOrganizacionalController::class, 'obtenerColoresCarnet']);
    
    // Rutas para empleados - quitamos el middleware temporalmente para diagnosticar
    Route::get('empleados', [EmpleadoController::class, 'index']);
    Route::get('empleados/{id}', [EmpleadoController::class, 'show']);
    Route::post('empleados', [EmpleadoController::class, 'store']);
    Route::put('empleados/{id}', [EmpleadoController::class, 'update']);
    Route::delete('empleados/{id}', [EmpleadoController::class, 'destroy']);
    Route::post('empleados/{id}/fincas', [EmpleadoController::class, 'asociarFincas']);
    
    // Rutas para tipos de contrato
    Route::get('tipos-contrato', [TipoContratoController::class, 'index']);
    Route::get('tipos-contrato/{id}', [TipoContratoController::class, 'show']);
    Route::post('tipos-contrato', [TipoContratoController::class, 'store']);
    Route::put('tipos-contrato/{id}', [TipoContratoController::class, 'update']);
    Route::delete('tipos-contrato/{id}', [TipoContratoController::class, 'destroy']);
    
    // Rutas para fincas
    Route::get('fincas', [FincaController::class, 'index']);
    Route::get('fincas/{id}', [FincaController::class, 'show']);
    Route::post('fincas', [FincaController::class, 'store']);
    Route::put('fincas/{id}', [FincaController::class, 'update']);
    Route::delete('fincas/{id}', [FincaController::class, 'destroy']);
    
    // Rutas para departamentos
    Route::get('departamentos', [DepartamentoController::class, 'index']);
    Route::get('departamentos/{id}', [DepartamentoController::class, 'show']);
    Route::post('departamentos', [DepartamentoController::class, 'store']);
    Route::put('departamentos/{id}', [DepartamentoController::class, 'update']);
    Route::delete('departamentos/{id}', [DepartamentoController::class, 'destroy']);
});