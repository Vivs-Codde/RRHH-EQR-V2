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
    Route::post('/assign-role', [RolePermissionController::class, 'assignRole']);
    Route::get('/users-with-roles', [RolePermissionController::class, 'getUsersWithRoles']);
    
    // Rutas para colores
    Route::middleware(['permission:ver colores'])->get('colores', [ColorController::class, 'index']);
    Route::middleware(['permission:ver colores'])->get('colores/{id}', [ColorController::class, 'show']);
    Route::middleware(['permission:crear colores'])->post('colores', [ColorController::class, 'store']);
    Route::middleware(['permission:editar colores'])->put('colores/{id}', [ColorController::class, 'update']);
    Route::middleware(['permission:eliminar colores'])->delete('colores/{id}', [ColorController::class, 'destroy']);
    
    // Rutas para estructuras organizacionales
    Route::middleware(['permission:ver estructuras'])->get('estructuras-organizacionales', [EstructuraOrganizacionalController::class, 'index']);
    Route::middleware(['permission:ver estructuras'])->get('estructuras-organizacionales/{id}', [EstructuraOrganizacionalController::class, 'show']);
    Route::middleware(['permission:crear estructuras'])->post('estructuras-organizacionales', [EstructuraOrganizacionalController::class, 'store']);
    Route::middleware(['permission:editar estructuras'])->put('estructuras-organizacionales/{id}', [EstructuraOrganizacionalController::class, 'update']);
    Route::middleware(['permission:eliminar estructuras'])->delete('estructuras-organizacionales/{id}', [EstructuraOrganizacionalController::class, 'destroy']);
    Route::middleware(['permission:editar estructuras'])->post('estructuras-organizacionales/{id}/colores', [EstructuraOrganizacionalController::class, 'asociarColores']);
    
    // Rutas para empleados
    Route::middleware(['permission:ver empleados'])->get('empleados', [EmpleadoController::class, 'index']);
    Route::middleware(['permission:ver empleados'])->get('empleados/{id}', [EmpleadoController::class, 'show']);
    Route::middleware(['permission:crear empleados'])->post('empleados', [EmpleadoController::class, 'store']);
    Route::middleware(['permission:editar empleados'])->put('empleados/{id}', [EmpleadoController::class, 'update']);
    Route::middleware(['permission:eliminar empleados'])->delete('empleados/{id}', [EmpleadoController::class, 'destroy']);
    Route::middleware(['permission:editar empleados'])->post('empleados/{id}/fincas', [EmpleadoController::class, 'asociarFincas']);
    
    // Rutas para tipos de contrato
    Route::middleware(['permission:ver tipos-contrato'])->get('tipos-contrato', [TipoContratoController::class, 'index']);
    Route::middleware(['permission:ver tipos-contrato'])->get('tipos-contrato/{id}', [TipoContratoController::class, 'show']);
    Route::middleware(['permission:crear tipos-contrato'])->post('tipos-contrato', [TipoContratoController::class, 'store']);
    Route::middleware(['permission:editar tipos-contrato'])->put('tipos-contrato/{id}', [TipoContratoController::class, 'update']);
    Route::middleware(['permission:eliminar tipos-contrato'])->delete('tipos-contrato/{id}', [TipoContratoController::class, 'destroy']);
    
    // Rutas para fincas
    Route::middleware(['permission:ver fincas'])->get('fincas', [FincaController::class, 'index']);
    Route::middleware(['permission:ver fincas'])->get('fincas/{id}', [FincaController::class, 'show']);
    Route::middleware(['permission:crear fincas'])->post('fincas', [FincaController::class, 'store']);
    Route::middleware(['permission:editar fincas'])->put('fincas/{id}', [FincaController::class, 'update']);
    Route::middleware(['permission:eliminar fincas'])->delete('fincas/{id}', [FincaController::class, 'destroy']);
    
    // Rutas para departamentos
    Route::middleware(['permission:ver departamentos'])->get('departamentos', [DepartamentoController::class, 'index']);
    Route::middleware(['permission:ver departamentos'])->get('departamentos/{id}', [DepartamentoController::class, 'show']);
    Route::middleware(['permission:crear departamentos'])->post('departamentos', [DepartamentoController::class, 'store']);
    Route::middleware(['permission:editar departamentos'])->put('departamentos/{id}', [DepartamentoController::class, 'update']);
    Route::middleware(['permission:eliminar departamentos'])->delete('departamentos/{id}', [DepartamentoController::class, 'destroy']);
});