# Centro de Costos - Documentación

## Resumen
Se ha creado exitosamente el módulo de **Centro de Costos** con relación a la tabla `tipo_contrato`.

## Estructura Creada

### 1. Migración
- **Archivo**: `database/migrations/2025_09_03_130527_create_centro_costos_table.php`
- **Tabla**: `centro_costos`
- **Campos**:
  - `id` (bigint, primary key, auto_increment)
  - `nombre` (string, 150 caracteres, único)
  - `estado` (boolean, default: true)
  - `grupo` (string, 100 caracteres, nullable)
  - `tipo_contrato_id` (foreign key hacia tipo_contrato)
  - `created_at` y `updated_at` (timestamps)

### 2. Modelo
- **Archivo**: `app/Models/CentroCosto.php`
- **Funcionalidades**:
  - Fillable: nombre, estado, grupo, tipo_contrato_id
  - Cast del campo `estado` como boolean
  - Relación `belongsTo` con TipoContrato
  - Scopes: `activos()` y `porGrupo()`
  - Documentación completa con anotaciones Swagger

### 3. Controlador API
- **Archivo**: `app/Http/Controllers/CentroCostoController.php`
- **Endpoints disponibles**:
  - `GET /api/centro-costos` - Listar centros de costo
  - `POST /api/centro-costos` - Crear centro de costo
  - `GET /api/centro-costos/{id}` - Obtener centro de costo específico
  - `PUT /api/centro-costos/{id}` - Actualizar centro de costo
  - `DELETE /api/centro-costos/{id}` - Eliminar centro de costo

### 4. Rutas
- **Archivo**: `routes/api.php`
- Todas las rutas protegidas con middleware `auth:sanctum`
- Rutas RESTful completas para el recurso centro-costos

### 5. Seeder
- **Archivo**: `database/seeders/CentroCostoSeeder.php`
- Datos de ejemplo insertados:
  - Administración General (Administrativo)
  - Recursos Humanos (Administrativo)
  - Producción Agrícola (Operacional)
  - Mantenimiento (Operacional)
  - Ventas y Marketing (Comercial)

### 6. Factory
- **Archivo**: `database/factories/CentroCostoFactory.php`
- Para generar datos de prueba
- Estados personalizados: `activo()`, `inactivo()`, `conGrupo()`

## Relaciones

### Centro de Costos → Tipo Contrato (Many-to-One)
```php
// En el modelo CentroCosto
public function tipoContrato()
{
    return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
}
```

### Tipo Contrato → Centros de Costo (One-to-Many)
```php
// En el modelo TipoContrato (actualizado)
public function centrosCosto()
{
    return $this->hasMany(CentroCosto::class, 'tipo_contrato_id');
}
```

## Ejemplos de Uso

### Crear un Centro de Costo
```php
POST /api/centro-costos
{
    "nombre": "Centro de Costos de Ejemplo",
    "estado": true,
    "grupo": "Administrativo",
    "tipo_contrato_id": 1
}
```

### Listar Centros de Costo con Filtros
```php
GET /api/centro-costos?estado=1&grupo=Operacional
```

### Usar en el Código
```php
// Obtener centros de costo activos
$centrosActivos = CentroCosto::activos()->get();

// Obtener centros de costo por grupo
$centrosOperacionales = CentroCosto::porGrupo('Operacional')->get();

// Obtener centro de costo con su tipo de contrato
$centro = CentroCosto::with('tipoContrato')->find(1);

// Obtener centros de costo de un tipo de contrato específico
$tipo = TipoContrato::find(1);
$centros = $tipo->centrosCosto;
```

## Validaciones Implementadas

### Creación (store)
- `nombre`: requerido, string, máximo 150 caracteres, único
- `estado`: opcional, boolean
- `grupo`: opcional, string, máximo 100 caracteres
- `tipo_contrato_id`: requerido, debe existir en tipo_contrato

### Actualización (update)
- `nombre`: opcional si se proporciona, string, máximo 150 caracteres, único (excepto el registro actual)
- `estado`: opcional, boolean
- `grupo`: opcional, string, máximo 100 caracteres
- `tipo_contrato_id`: opcional si se proporciona, debe existir en tipo_contrato

## Estado Actual
✅ Migración ejecutada exitosamente
✅ Modelo creado con relaciones
✅ Controlador API completo
✅ Rutas configuradas
✅ Datos de ejemplo insertados
✅ Factory para pruebas
✅ Documentación Swagger incluida

## Próximos Pasos Sugeridos
1. Implementar tests unitarios y de integración
2. Agregar middleware de autorización específico si es necesario
3. Considerar agregar soft deletes si se requiere
4. Implementar cache para consultas frecuentes
5. Agregar validaciones adicionales según reglas de negocio
