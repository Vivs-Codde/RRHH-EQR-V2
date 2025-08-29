<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Departamentos",
 *     description="API para gestión de departamentos"
 * )
 */
class DepartamentoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/departamentos",
     *     summary="Listar todos los departamentos",
     *     description="Obtiene una lista paginada de todos los departamentos con opciones de filtrado",
     *     tags={"Departamentos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página para paginación",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad de registros por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Búsqueda por nombre del departamento",
     *         required=false,
     *         @OA\Schema(type="string", example="Recursos Humanos")
     *     ),
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Filtrar por estado (activo/inactivo)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de departamentos obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=35),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Departamento")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $query = Departamento::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        if ($request->boolean('with_estructuras')) {
            $query->with('estructurasOrganizacionales');
        }

        $departamentos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $departamentos
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/departamentos",
     *     summary="Crear un nuevo departamento",
     *     description="Crea un nuevo departamento en el sistema",
     *     tags={"Departamentos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Tecnología e Innovación", description="Nombre del departamento"),
     *             @OA\Property(property="descripcion", type="string", example="Departamento encargado del desarrollo tecnológico", description="Descripción detallada del departamento"),
     *             @OA\Property(property="codigo", type="string", example="TI", description="Código identificador del departamento"),
     *             @OA\Property(property="ubicacion", type="string", example="Piso 3, Edificio Central", description="Ubicación física del departamento"),
     *             @OA\Property(property="telefono", type="string", example="3201234567", description="Teléfono del departamento"),
     *             @OA\Property(property="email", type="string", example="ti@empresa.com", description="Email del departamento"),
     *             @OA\Property(property="presupuesto", type="number", format="decimal", example=150000.00, description="Presupuesto asignado al departamento"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado del departamento (activo/inactivo)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Departamento creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Departamento creado correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Departamento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="nombre", type="array", @OA\Items(type="string", example="El campo nombre es obligatorio.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El campo email debe ser una dirección de correo válida."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100|unique:departamentos,nombre',
                'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'estado' => 'boolean'
            ]);

            $validated['estado'] = $validated['estado'] ?? true;
            $departamento = Departamento::create($validated);

            return response()->json([
                'success' => true,
                'data' => $departamento,
                'message' => 'Departamento creado exitosamente'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/departamentos/{id}",
     *     summary="Obtener un departamento específico",
     *     description="Retorna la información de un departamento por su ID",
     *     tags={"Departamentos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del departamento",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="with_estructuras",
     *         in="query",
     *         description="Incluir estructuras organizacionales relacionadas",
     *         required=false,
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Departamento encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Departamento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Departamento no encontrado"
     *     )
     * )
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $query = Departamento::query();

        if ($request->boolean('with_estructuras')) {
            $query->with('estructurasOrganizacionales');
        }

        $departamento = $query->find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $departamento
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/departamentos/{id}",
     *     summary="Actualizar un departamento",
     *     description="Actualiza un departamento existente",
     *     tags={"Departamentos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del departamento a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Tecnología e Innovación Digital", description="Nombre del departamento"),
     *             @OA\Property(property="descripcion", type="string", example="Departamento encargado del desarrollo tecnológico y transformación digital", description="Descripción detallada del departamento"),
     *             @OA\Property(property="codigo", type="string", example="TID", description="Código identificador del departamento"),
     *             @OA\Property(property="ubicacion", type="string", example="Piso 4, Edificio Central", description="Ubicación física del departamento"),
     *             @OA\Property(property="telefono", type="string", example="3209876543", description="Teléfono del departamento"),
     *             @OA\Property(property="email", type="string", example="tid@empresa.com", description="Email del departamento"),
     *             @OA\Property(property="presupuesto", type="number", format="decimal", example=200000.00, description="Presupuesto asignado al departamento"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado del departamento (activo/inactivo)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Departamento actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Departamento actualizado correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Departamento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Departamento no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'nombre' => 'sometimes|required|string|max:100|unique:departamentos,nombre,' . $id,
                'color' => 'sometimes|nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'estado' => 'sometimes|boolean'
            ]);

            $departamento->update($validated);

            return response()->json([
                'success' => true,
                'data' => $departamento->fresh(),
                'message' => 'Departamento actualizado exitosamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/departamentos/{id}",
     *     summary="Eliminar un departamento",
     *     description="Elimina un departamento existente. Nota: Solo se puede eliminar si no tiene empleados o estructuras organizacionales asociadas.",
     *     tags={"Departamentos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del departamento a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Departamento eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Departamento eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Departamento no encontrado"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflicto - No se puede eliminar porque tiene dependencias asociadas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se puede eliminar el departamento porque tiene empleados o estructuras organizacionales asociadas")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        if ($departamento->estructurasOrganizacionales()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el departamento porque tiene estructuras organizacionales asociadas'
            ], 409);
        }

        $departamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departamento eliminado exitosamente'
        ]);
    }
}
