<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Empleados",
 *     description="API para gestión de empleados"
 * )
 */
class EmpleadoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/empleados",
     *     summary="Listar todos los empleados",
     *     description="Obtiene una lista paginada de todos los empleados con opciones de filtrado avanzadas",
     *     tags={"Empleados"},
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
     *         description="Búsqueda por nombre_As2, apellido_As2 o idempleado_As2",
     *         required=false,
     *         @OA\Schema(type="string", example="Juan Pérez")
     *     ),
     *     @OA\Parameter(
     *         name="finca",
     *         in="query",
     *         description="Filtrar por ID de finca",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="tipo_contrato_id",
     *         in="query",
     *         description="Filtrar por ID de tipo de contrato",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="estructura_organizacional_id",
     *         in="query",
     *         description="Filtrar por ID de estructura organizacional",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="estado_rrhh",
     *         in="query",
     *         description="Filtrar por estado RRHH",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="estado_As2",
     *         in="query",
     *         description="Filtrar por estado AS2",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=142),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Empleado")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $query = Empleado::query();

        if ($request->boolean('with_relations')) {
            $query->conRelaciones();
        }

        if ($request->has('estado_rrhh')) {
            $query->where('estado_rrhh', $request->boolean('estado_rrhh'));
        }

        if ($request->has('estado_As2')) {
            $query->where('estado_As2', $request->boolean('estado_As2'));
        }

        if ($request->has('tipo_contrato_id')) {
            $query->where('tipo_contrato_id', $request->get('tipo_contrato_id'));
        }

        if ($request->has('estructura_organizacional_id')) {
            $query->where('estructura_organizacional_id', $request->get('estructura_organizacional_id'));
        }

        $empleados = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $empleados
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/empleados",
     *     summary="Crear un nuevo empleado",
     *     description="Crea un nuevo empleado en el sistema con información completa",
     *     tags={"Empleados"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idempleado_As2", "nombre_As2", "apellido_As2", "fechaNacimiento_As2", "contacto", "discapacidad_As2", "porcentaje_discapacidad_As2", "fechaIngreso_As2", "estructuraCosto_As2", "idSrv66", "idSrv90", "idAreas", "tipoUserBiometrico", "tipo_contrato_id", "estructura_organizacional_id"},
     *             @OA\Property(property="idempleado_As2", type="string", example="EMP001", maxLength=10, description="ID del empleado en AS2"),
     *             @OA\Property(property="estado_rrhh", type="boolean", example=true, description="Estado RRHH"),
     *             @OA\Property(property="estado_As2", type="boolean", example=true, description="Estado AS2"),
     *             @OA\Property(property="nombre_As2", type="string", example="Juan", maxLength=20, description="Nombre del empleado"),
     *             @OA\Property(property="apellido_As2", type="string", example="Pérez", maxLength=20, description="Apellido del empleado"),
     *             @OA\Property(property="fechaNacimiento_As2", type="string", format="date", example="1990-01-15", description="Fecha de nacimiento"),
     *             @OA\Property(property="contacto", type="string", maxLength=10, example="3001234567", description="Contacto telefónico"),
     *             @OA\Property(property="discapacidad_As2", type="string", example="Ninguna", maxLength=20, description="Discapacidad"),
     *             @OA\Property(property="porcentaje_discapacidad_As2", type="integer", example=0, description="Porcentaje de discapacidad"),
     *             @OA\Property(property="fechaIngreso_As2", type="string", format="date", example="2020-03-01", description="Fecha de ingreso AS2"),
     *             @OA\Property(property="fechaSalida_As2", type="string", format="date", example=null, description="Fecha de salida AS2"),
     *             @OA\Property(property="estructuraCosto_As2", type="string", example="CC001", description="Estructura de costo AS2"),
     *             @OA\Property(property="fechaIngreso_rrhh", type="string", format="date", example="2020-03-01", description="Fecha de ingreso RRHH"),
     *             @OA\Property(property="fechaSalida_rrhh", type="string", format="date", example=null, description="Fecha de salida RRHH"),
     *             @OA\Property(property="idSrv66", type="string", example="SRV66001", description="ID Srv66"),
     *             @OA\Property(property="idSrv90", type="string", example="SRV90001", description="ID Srv90"),
     *             @OA\Property(property="idAreas", type="string", example="AREA001", description="ID Areas"),
     *             @OA\Property(property="tipoUserBiometrico", type="string", example="TIPO1", description="Tipo usuario biométrico"),
     *             @OA\Property(property="foto_perfil", type="string", example="profile.jpg", description="URL foto de perfil"),
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID del usuario asociado"),
     *             @OA\Property(property="tipo_contrato_id", type="integer", example=1, description="ID del tipo de contrato"),
     *             @OA\Property(property="estructura_organizacional_id", type="integer", example=1, description="ID de la estructura organizacional"),
     *             @OA\Property(property="fincas", type="array", description="Lista de IDs de fincas asociadas", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empleado creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empleado creado correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Empleado")
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
     *                 @OA\Property(property="nombre_As2", type="array", @OA\Items(type="string", example="El campo nombre_As2 es obligatorio.")),
     *                 @OA\Property(property="idempleado_As2", type="array", @OA\Items(type="string", example="El ID de empleado ya está en uso.")),
     *                 @OA\Property(property="contacto", type="array", @OA\Items(type="string", example="El campo contacto es obligatorio."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'idempleado_As2' => 'required|string|max:10|unique:empleados,idempleado_As2',
                'estado_rrhh' => 'boolean',
                'estado_As2' => 'boolean',
                'nombre_As2' => 'required|string|max:20',
                'apellido_As2' => 'required|string|max:20',
                'fechaNacimiento_As2' => 'required|date',
                'contacto' => 'required|string|max:10',
                'discapacidad_As2' => 'required|string|max:20',
                'porcentaje_discapacidad_As2' => 'required|integer|min:0|max:100',
                'fechaIngreso_As2' => 'required|date',
                'fechaSalida_As2' => 'nullable|date',
                'estructuraCosto_As2' => 'required|string|max:255',
                'fechaIngreso_rrhh' => 'nullable|date',
                'fechaSalida_rrhh' => 'nullable|date',
                'idSrv66' => 'required|string|max:255',
                'idSrv90' => 'required|string|max:255',
                'idAreas' => 'required|string|max:255',
                'tipoUserBiometrico' => 'required|string|max:255',
                'foto_perfil' => 'nullable|string|max:255',
                'user_id' => 'nullable|integer|exists:users,id|unique:empleados,user_id',
                'tipo_contrato_id' => 'required|integer|exists:tipo_contrato,id',
                'estructura_organizacional_id' => 'required|integer|exists:estructura_organizacional,id',
                'fincas' => 'sometimes|array',
                'fincas.*' => 'integer|exists:fincas,id'
            ]);

            $validated['estado_rrhh'] = $validated['estado_rrhh'] ?? true;
            $validated['estado_As2'] = $validated['estado_As2'] ?? true;

            $empleado = Empleado::create(collect($validated)->except('fincas')->toArray());

            if (isset($validated['fincas'])) {
                $empleado->fincas()->sync($validated['fincas']);
            }

            $empleado->load(['user', 'tipoContrato', 'estructuraOrganizacional', 'fincas']);

            return response()->json([
                'success' => true,
                'data' => $empleado,
                'message' => 'Empleado creado exitosamente'
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
     *     path="/api/empleados/{id}",
     *     summary="Obtener un empleado específico",
     *     description="Retorna la información completa de un empleado por su ID",
     *     tags={"Empleados"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="with_relations",
     *         in="query",
     *         description="Incluir relaciones (finca, tipo_contrato, departamento)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Empleado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $query = Empleado::query();

        if ($request->boolean('with_relations')) {
            $query->conRelaciones();
        }

        $empleado = $query->find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $empleado
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/empleados/{id}",
     *     summary="Actualizar un empleado",
     *     description="Actualiza la información de un empleado existente",
     *     tags={"Empleados"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="idempleado_As2", type="string", example="EMP001", maxLength=10, description="ID del empleado en AS2"),
     *             @OA\Property(property="estado_rrhh", type="boolean", example=true, description="Estado RRHH"),
     *             @OA\Property(property="estado_As2", type="boolean", example=true, description="Estado AS2"),
     *             @OA\Property(property="nombre_As2", type="string", example="Juan", maxLength=20, description="Nombre del empleado"),
     *             @OA\Property(property="apellido_As2", type="string", example="Pérez", maxLength=20, description="Apellido del empleado"),
     *             @OA\Property(property="fechaNacimiento_As2", type="string", format="date", example="1990-01-15", description="Fecha de nacimiento"),
     *             @OA\Property(property="contacto", type="string", maxLength=10, example="3001234567", description="Contacto telefónico"),
     *             @OA\Property(property="discapacidad_As2", type="string", example="Ninguna", maxLength=20, description="Discapacidad"),
     *             @OA\Property(property="porcentaje_discapacidad_As2", type="integer", example=0, description="Porcentaje de discapacidad"),
     *             @OA\Property(property="fechaIngreso_As2", type="string", format="date", example="2020-03-01", description="Fecha de ingreso AS2"),
     *             @OA\Property(property="fechaSalida_As2", type="string", format="date", example=null, description="Fecha de salida AS2"),
     *             @OA\Property(property="estructuraCosto_As2", type="string", example="CC001", description="Estructura de costo AS2"),
     *             @OA\Property(property="fechaIngreso_rrhh", type="string", format="date", example="2020-03-01", description="Fecha de ingreso RRHH"),
     *             @OA\Property(property="fechaSalida_rrhh", type="string", format="date", example=null, description="Fecha de salida RRHH"),
     *             @OA\Property(property="idSrv66", type="string", example="SRV66001", description="ID Srv66"),
     *             @OA\Property(property="idSrv90", type="string", example="SRV90001", description="ID Srv90"),
     *             @OA\Property(property="idAreas", type="string", example="AREA001", description="ID Areas"),
     *             @OA\Property(property="tipoUserBiometrico", type="string", example="TIPO1", description="Tipo usuario biométrico"),
     *             @OA\Property(property="foto_perfil", type="string", example="profile.jpg", description="URL foto de perfil"),
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID del usuario asociado"),
     *             @OA\Property(property="tipo_contrato_id", type="integer", example=1, description="ID del tipo de contrato"),
     *             @OA\Property(property="estructura_organizacional_id", type="integer", example=1, description="ID de la estructura organizacional"),
     *             @OA\Property(property="fincas", type="array", description="Lista de IDs de fincas asociadas", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empleado actualizado correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Empleado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'idempleado_As2' => 'sometimes|required|string|max:10|unique:empleados,idempleado_As2,' . $id,
                'estado_rrhh' => 'sometimes|boolean',
                'estado_As2' => 'sometimes|boolean',
                'nombre_As2' => 'sometimes|required|string|max:20',
                'apellido_As2' => 'sometimes|required|string|max:20',
                'fechaNacimiento_As2' => 'sometimes|required|date',
                'contacto' => 'sometimes|required|string|max:10',
                'discapacidad_As2' => 'sometimes|required|string|max:20',
                'porcentaje_discapacidad_As2' => 'sometimes|required|integer|min:0|max:100',
                'fechaIngreso_As2' => 'sometimes|required|date',
                'fechaSalida_As2' => 'sometimes|nullable|date',
                'estructuraCosto_As2' => 'sometimes|required|string|max:255',
                'fechaIngreso_rrhh' => 'sometimes|nullable|date',
                'fechaSalida_rrhh' => 'sometimes|nullable|date',
                'idSrv66' => 'sometimes|required|string|max:255',
                'idSrv90' => 'sometimes|required|string|max:255',
                'idAreas' => 'sometimes|required|string|max:255',
                'tipoUserBiometrico' => 'sometimes|required|string|max:255',
                'foto_perfil' => 'sometimes|nullable|string|max:255',
                'user_id' => 'sometimes|nullable|integer|exists:users,id|unique:empleados,user_id,' . $id,
                'tipo_contrato_id' => 'sometimes|required|integer|exists:tipo_contrato,id',
                'estructura_organizacional_id' => 'sometimes|required|integer|exists:estructura_organizacional,id',
                'fincas' => 'sometimes|array',
                'fincas.*' => 'integer|exists:fincas,id'
            ]);

            $empleado->update(collect($validated)->except('fincas')->toArray());

            if (isset($validated['fincas'])) {
                $empleado->fincas()->sync($validated['fincas']);
            }

            $empleado->load(['user', 'tipoContrato', 'estructuraOrganizacional', 'fincas']);

            return response()->json([
                'success' => true,
                'data' => $empleado,
                'message' => 'Empleado actualizado exitosamente'
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
     *     path="/api/empleados/{id}",
     *     summary="Eliminar un empleado",
     *     description="Elimina un empleado del sistema (desactivación lógica)",
     *     tags={"Empleados"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empleado eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $empleado->fincas()->detach();
        $empleado->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empleado eliminado exitosamente'
        ]);
    }
}
