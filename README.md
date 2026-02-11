# GBB PHP MVC - Plataforma de Gestión y Compra de Juegos

Sistema web basado en arquitectura MVC desarrollado en PHP con SQL Server como base de datos federada. Gestiona catálogo de juegos, carrito de compras, órdenes, inventario de claves de juego y operaciones de administración.

## Requisitos del Sistema

### Software Base
- **XAMPP** (Apache 2.4.x + PHP) o servidor web compatible
- **PHP 8.0+** con soporte para SQL Server
- **SQL Server 2019+** accesible en la red (local o remota)

### Extensiones PHP Requeridas
Las extensiones deben estar habilitadas en `php.ini` de XAMPP:
```ini
extension=php_pdo_sqlsrv.dll
extension=php_sqlsrv.dll
extension=php_ldap.dll  (opcional, para LDAP auth)
```

### Controlador ODBC
- **Microsoft ODBC Driver 17 o 18** para SQL Server
  - Descargar desde: https://learn.microsoft.com/es-es/sql/connect/odbc/download-odbc-driver-for-sql-server

## Instalación y Configuración

### Paso 1: Descargar y Ubicar el Proyecto
```bash
# Clonar o copiar el proyecto en la carpeta de XAMPP
cd C:\xampp\htdocs
git clone <repo-url> gbb-php-mvc
# O simplemente copiar la carpeta del proyecto
```

**Ruta esperada:** `C:\xampp\htdocs\gbb-php-mvc\`

### Paso 2: Configurar la Conexión a SQL Server

Editar `models/DB.php` y verificar las credenciales de conexión:

```php
// Definir servidor, usuario y contraseña
define('SQL_SERVER', '10.26.208.203');      // IP del servidor SQL Server
define('SQL_DATABASE', 'GBB_Host');         // Base de datos local
define('SQL_USER', 'app_gbb');              // Usuario SQL
define('SQL_PASSWORD', 'tu_contraseña');    // Contraseña
```

**Servidor Remoto Federado:**
El sistema usa un linked server para conectar con `GBB_Remoto` en `[10.26.208.149]` para datos de usuarios, juegos e inventario.

### Paso 3: Verificar Extensiones PHP

1. Abre XAMPP Control Panel
2. Haz click en "Config" de Apache → `php.ini`
3. Busca las líneas:
   ```ini
   extension=php_pdo_sqlsrv.dll
   extension=php_sqlsrv.dll
   ```
4. Si están comentadas (`;` al inicio), descomenta quitando el `;`
5. **Guarda y reinicia Apache**

**Verificar instalación:**
```bash
# Crear archivo phpinfo.php en C:\xampp\htdocs\
<?php phpinfo(); ?>

# Acceder desde navegador
# http://localhost/phpinfo.php
# Buscar "sqlsrv" en la página
```

### Paso 4: Crear Bases de Datos (si es necesario)

Ejecutar scripts de inicialización en SQL Server Management Studio:

```bash
C:\xampp\htdocs\gbb-php-mvc\scripts\
├── 01_create_gbb_host.sql      # Base de datos local (órdenes, entregas)
└── 02_create_gbb_remoto.sql    # Base de datos remota (usuarios, juegos)
```

**Ejecución:**
```sql
-- Conectar a SQL Server y ejecutar
USE master;
GO
-- Ejecutar script 01
-- Luego script 02
```

## Estructura del Proyecto

```
gbb-php-mvc/
├── controllers/           # Controladores (lógica de negocio)
│   ├── AuthController.php     # Autenticación
│   ├── CatalogController.php  # Catálogo de juegos
│   ├── carrito.php            # Gestión de carrito
│   ├── admin/                 # Paneles administrativos
│   └── vendedor/              # Panel de vendedor
│
├── models/                # Capas de acceso a datos
│   ├── DB.php             # Conexión singleton a SQL Server
│   ├── SP.php             # Wrapper inteligente para stored procedures
│   ├── AuthModel.php      # Autenticación de usuarios
│   ├── GameModel.php      # CRUD de juegos
│   ├── UserModel.php      # CRUD de usuarios
│   ├── OrdenesModel.php   # Procesamiento de compras (transacciones)
│   ├── InventarioModel.php # Gestión de inventario de claves
│   └── ...más modelos
│
├── views/                 # Vistas y Templates HTML/PHP
│   ├── auth/              # Vistas de autenticación
│   ├── admin/             # Vistas de administración
│   ├── catalog/           # Vistas del catálogo
│   ├── carrito/           # Vistas del carrito
│   └── partials/          # Componentes reutilizables
│
├── helpers/               # Funciones auxiliares
│   ├── Auth.php           # Control de roles y permisos
│   └── Logger.php         # Logging de auditoría
│
├── scripts/               # Scripts SQL de inicialización
│   ├── 01_create_gbb_host.sql
│   └── 02_create_gbb_remoto.sql
│
├── index.php              # Punto de entrada con router básico
└── README.md              # Este archivo
```

## Iniciar la Aplicación

### Opción 1: Después de Reiniciar Apache

1. **Abre XAMPP Control Panel**
2. Asegúrate de que Apache esté corriendo (Status debe mostrar "Running")
3. Si no está corriendo, haz click en **"Start"** button de Apache
4. Abre tu navegador y accede a:
   ```
   http://localhost/gbb-php-mvc/controllers/login.php
   ```

### Opción 2: Usar index.php (Router)

```
http://localhost/gbb-php-mvc/
```

El `index.php` de la raíz detectará automáticamente y enrutará a `login.php` si no hay sesión activa.

### Opción 3: Acceso Directo por Rol

```
Cliente:
http://localhost/gbb-php-mvc/controllers/catalogo.php

Vendedor:
http://localhost/gbb-php-mvc/controllers/vendor/publicaciones.php

Administrador:
http://localhost/gbb-php-mvc/controllers/admin/usuarios.php
```

## Credenciales de Prueba

Las siguientes credenciales están disponibles en la base de datos `GBB_Remoto` para testing:

| Rol | Email | Contraseña | Función |
|-----|-------|-----------|---------|
| Cliente | `cliente@gbb.com` | `123456` | Navegar catálogo, comprar |
| Vendedor | `vendedor@gbb.com` | `123456` | Gestionar inventario de claves |
| Administrador | `admin@gbb.com` | `123456` | Gestión completa del sistema |

⚠️ **IMPORTANTE:** Cambiar contraseñas en producción.

## Flujos Principales de la Aplicación

### 1. Autenticación (`AuthController.php`)
- Valida credenciales contra tabla `Usuarios` en `GBB_Remoto`
- Crea sesión global con datos del usuario
- Implementa control de roles via `helpers/Auth.php`

### 2. Catálogo y Compra (`CatalogController.php`, `carrito.php`)
- Lista juegos disponibles via `sp_Catalogo_ListarJuegos`
- Enriquece inventario desde `KeysInventario`
- Maneja carrito en sesión

### 3. Cierre de Compra (`OrdenesModel.php` - Transaccional)
- Crea orden en tabla `Ordenes` (host)
- Inserta detalles en `DetalleOrden`
- Asigna claves disponibles de `KeysInventario` (remoto)
- Marca claves como `VENDIDA`
- **Rollback automático si falla cualquier paso**

### 4. Gestión de Inventario (`InventarioModel.php`)
- Listar claves disponibles
- Aprobar/rechazar claves pendientes
- Registrar entregas

### 5. Panel Administrativo (`admin/`)
- CRUD de juegos
- CRUD de usuarios
- Listado de órdenes
- Auditoría de operaciones
- Reportes de ventas

## Modelos y Stored Procedures

Cada modelo data-access incluye métodos que invocan stored procedures específicos. Ejemplos:

```php
// GameModel.php
$games = GameModel::listarJuegos();           // sp_Juegos_Listar
$game = GameModel::obtenerJuego(5);           // sp_Juegos_Obtener

// UserModel.php  
$users = UserModel::listarUsuarios();         // sp_Usuarios_Listar
UserModel::crearUsuario($data);              // sp_Usuarios_Crear

// OrdenesModel.php (Transactional)
OrdenesModel::confirmarCompra($clienteId, $items);  // Múltiples SPs + DDL
```

**Referencia completa de SPs:** Ver comentarios en archivos `models/*.php`

## Solución de Problemas

### Error de Conexión SQL Server

**Síntoma:** "Could not find named pipe provider"

**Solución:**
1. Verifica servidor y puerto en `models/DB.php`:
   ```php
   'Server' => '10.26.208.203,1433'  // Incluir puerto
   ```
2. Confirma conectividad:
   ```bash
   # Desde CMD o PowerShell
   ping 10.26.208.203
   telnet 10.26.208.203 1433
   ```
3. Verifica credenciales SQL Server:
   ```sql
   -- En SQL Server Management Studio
   USE GBB_Host;
   GO
   ```

### Error "Undefined function pdo_sqlsrv"

**Síntoma:** Fatal error en DB.php

**Solución:**
1. Verifica `phpinfo()` - busca sección "sqlsrv"
2. Si no aparece, descarga extensiones:
   - https://learn.microsoft.com/es-es/sql/connect/php/download-drivers-php-sql-server
3. Descomenta en `php.ini`:
   ```ini
   extension=php_pdo_sqlsrv.dll
   extension=php_sqlsrv.dll
   ```
4. **Reinicia Apache completamente** (Stop + Start)

### Pantalla en Blanco o Error 500

**Solución:**
1. Habilita error reporting en `index.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', '1');
   ```
2. Revisa logs:
   - **Apache:** `C:\xampp\apache\logs\error.log`
   - **PHP:** `C:\xampp\php\error.log`
3. Busca mensajes de excepción o SQL error

### Transacciones de Compra Falla

**Como revisar:**
- La tabla `Ordenes` no tiene registro (crearOrden falló)
- Falta la clave en `DetalleOrden.GameKey`
- Clave no marcada como VENDIDA

**Debugging:**
```php
// En OrdenesModel.php, agrega logging
error_log("OrdenId: $ordenId");
error_log("Total: $total");
error_log($e->getMessage());  // Excepción del modelo
```

### Permiso Denegado en Tablas Remoto

**Síntoma:** "The user does not have permission..."

**Solución:**
1. Verifica permisos SQL user `app_gbb` en `GBB_Remoto`:
   ```sql
   GRANT SELECT, INSERT, UPDATE ON dbo.Usuarios TO [app_gbb];
   GRANT SELECT, INSERT, UPDATE ON dbo.Juegos TO [app_gbb];
   GRANT EXECUTE ON dbo.sp_Key_Vender TO [app_gbb];
   ```
2. Especifica usuarios desde linked server:
   ```sql
   GRANT SELECT ON [10.26.208.149].GBB_Remoto.dbo.Usuarios TO [app_gbb];
   ```

## Desarrollo y Mejoras

### Agregar nuevo Stored Procedure

1. Crea el SP en SQL Server:
   ```sql
   CREATE PROCEDURE sp_NOM_MiNuevoProceso
       @Parametro1 INT,
       @Parametro2 NVARCHAR(100)
   AS
   BEGIN
       -- Lógica
   END;
   GO
   ```

2. Crea método en modelo correspondiente:
   ```php
   public static function miNuevoProceso(int $param1, string $param2): array {
       require_once __DIR__ . '/SP.php';
       return SP::call('dbo.sp_NOM_MiNuevoProceso', [
           'Parametro1' => $param1,
           'Parametro2' => $param2,
       ]);
   }
   ```

3. Usa desde controlador:
   ```php
   $resultado = MiModel::miNuevoProceso(42, "valor");
   ```

### Agregar nueva Vista

1. Crea archivo `.php` en `views/` con estructura HTML
2. Referencia desde controlador:
   ```php
   require_once __DIR__ . '/../views/mi_vista.php';
   ```

## Caching y Sesiones

- **Sesiones:** Iniciadas en `AuthController.php`, almacenadas en `$_SESSION`
- **Carrito:** Guardado en sesión para compra multi-paso
- **Cache:** No implementado actualmente (considerar en producción)

## Seguridad

- ✅ Prepared statements (binding de parámetros)
- ✅ Control de roles en `helpers/Auth.php`
- ✅ Validación de POST data
- ⚠️ CSRF: Implementar tokens CSRF en producción
- ⚠️ HTTPS: Usar siempre en producción
- ⚠️ Contraseñas: Usar `password_hash()` en lugar de plain text

## Performance y Escalabilidad

- **Conexión BD:** Singleton en `DB::conn()`
- **Índices:** Verificar índices en tablas principales (Ordenes, DetalleOrden, KeysInventario)
- **Stored Procedures:** Compilados en SQL Server, optimizados
- **Linked Server:** Considerar replicación para datos de alta lectura

## Contacto y Soporte

Para reportar bugs o solicitar features, contacta al equipo de desarrollo.

---

**Última actualización:** Febrero 2026  
**Versión:** 1.0.0-beta
