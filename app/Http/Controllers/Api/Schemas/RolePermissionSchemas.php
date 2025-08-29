<?php

namespace App\Http\Controllers\Api\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     title="Role",
 *     description="Modelo de rol para el sistema",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="admin"),
 *     @OA\Property(property="guard_name", type="string", example="web"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T12:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T12:00:00.000000Z"),
 *     @OA\Property(
 *         property="permissions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Permission")
 *     )
 * )
 */
class RoleSchema
{
}

/**
 * @OA\Schema(
 *     schema="Permission",
 *     type="object",
 *     title="Permission",
 *     description="Modelo de permiso para el sistema",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="crear usuarios"),
 *     @OA\Property(property="guard_name", type="string", example="web"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T12:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T12:00:00.000000Z")
 * )
 */
class PermissionSchema
{
}
