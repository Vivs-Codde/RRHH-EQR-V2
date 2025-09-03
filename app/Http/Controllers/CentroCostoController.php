<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CentroCosto;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Centros de Costo",
 *     description="API para gestión de centros de costo"
 * )
 */
class CentroCostoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/centro-costos",
     *     summary="Listar centros de costo",
     *     tags={"Centros de Costo"},
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Filtrar por estado (1=activo, 0=inactivo)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="grupo",
     *         in="query",
     *         description="Filtrar por grupo",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de centros de costo",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CentroCosto")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = CentroCosto::with('tipoContrato');
        
        if ($request->has('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }
        
        if ($request->filled('grupo')) {
            $query->porGrupo($request->get('grupo'));
        }
        
        $centrosCosto = $query->orderBy('nombre')->get();
        
        return response()->json($centrosCosto);
    }

    /**
     * @OA\Post(
     *     path="/api/centro-costos",
     *     summary="Crear centro de costo",
     *     tags={"Centros de Costo"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "tipo_contrato_id"},
     *             @OA\Property(property="nombre", type="string", maxLength=150, example="Nuevo Centro de Costo"),
     *             @OA\Property(property="estado", type="boolean", example=true),
     *             @OA\Property(property="grupo", type="string", maxLength=100, example="Administrativo"),
     *             @OA\Property(property="tipo_contrato_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Centro de costo creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/CentroCosto")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:150|unique:centro_costos,nombre',
                'estado' => 'boolean',
                'grupo' => 'nullable|string|max:100',
                'tipo_contrato_id' => 'required|exists:tipo_contrato,id',
            ]);
            
            $centroCosto = CentroCosto::create($validatedData);
            $centroCosto->load('tipoContrato');
            
            return response()->json($centroCosto, 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/centro-costos/{id}",
     *     summary="Obtener centro de costo por ID",
     *     tags={"Centros de Costo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del centro de costo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Centro de costo encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/CentroCosto")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de costo no encontrado"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $centroCosto = CentroCosto::with('tipoContrato')->find($id);
        
        if (!$centroCosto) {
            return response()->json([
                'message' => 'Centro de costo no encontrado'
            ], 404);
        }
        
        return response()->json($centroCosto);
    }

    /**
     * @OA\Put(
     *     path="/api/centro-costos/{id}",
     *     summary="Actualizar centro de costo",
     *     tags={"Centros de Costo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del centro de costo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", maxLength=150),
     *             @OA\Property(property="estado", type="boolean"),
     *             @OA\Property(property="grupo", type="string", maxLength=100),
     *             @OA\Property(property="tipo_contrato_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Centro de costo actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/CentroCosto")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de costo no encontrado"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $centroCosto = CentroCosto::find($id);
        
        if (!$centroCosto) {
            return response()->json([
                'message' => 'Centro de costo no encontrado'
            ], 404);
        }
        
        try {
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:150|unique:centro_costos,nombre,' . $id,
                'estado' => 'sometimes|boolean',
                'grupo' => 'nullable|string|max:100',
                'tipo_contrato_id' => 'sometimes|required|exists:tipo_contrato,id',
            ]);
            
            $centroCosto->update($validatedData);
            $centroCosto->load('tipoContrato');
            
            return response()->json($centroCosto);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/centro-costos/{id}",
     *     summary="Eliminar centro de costo",
     *     tags={"Centros de Costo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del centro de costo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Centro de costo eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de costo no encontrado"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $centroCosto = CentroCosto::find($id);
        
        if (!$centroCosto) {
            return response()->json([
                'message' => 'Centro de costo no encontrado'
            ], 404);
        }
        
        $centroCosto->delete();
        
        return response()->json([
            'message' => 'Centro de costo eliminado exitosamente'
        ]);
    }
}
