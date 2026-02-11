/*
  Configuración mejorada Linked Server para GBB

  - Variables parametrizables para adaptar al entorno
  - Manejo básico de errores (TRY/CATCH)
  - Validaciones y pasos de prueba al final
  - ADVERTENCIA: evitar dejar credenciales en texto plano en producción
*/

USE [master];
GO

DECLARE
    @LinkedServerName sysname   = N'10.26.208.149', -- nombre del linked server (puede ser IP o alias)
    @DataSource       sysname   = N'10.26.208.149', -- host/datasource
    @RemoteDatabase   sysname   = N'GBB_Remoto',    -- base remota esperada
    @LocalDatabase    sysname   = N'GBB_Host',      -- base local usada por la app
    @Provider         sysname   = N'MSOLEDBSQL',   -- ajustar si es necesario (SQLNCLI11, SQLNCLI, OLEDB)
    @RemoteUser       sysname   = N'REMOTE_SQL_USER',
    @RemotePassword   nvarchar(128) = N'REMOTE_SQL_PASSWORD', -- considere almacenar en credenciales seguras
    @DropIfExists     bit       = 1; -- 1 = dropear y recrear, 0 = no tocar si existe

-- Mensaje inicial
PRINT 'Iniciando configuración de linked server: ' + @LinkedServerName;

BEGIN TRY

    IF EXISTS (SELECT 1 FROM sys.servers WHERE name = @LinkedServerName)
    BEGIN
        IF @DropIfExists = 1
        BEGIN
            PRINT 'Linked server existente detectado; eliminando (droplogins).' ;
            EXEC master.dbo.sp_dropserver @server = @LinkedServerName, @droplogins = 'droplogins';
        END
        ELSE
        BEGIN
            PRINT 'Linked server ya existe y @DropIfExists = 0; se omite recreación.';
        END
    END

    IF NOT EXISTS (SELECT 1 FROM sys.servers WHERE name = @LinkedServerName)
    BEGIN
        PRINT 'Creando linked server ' + @LinkedServerName + ' usando provider ' + @Provider;
        EXEC master.dbo.sp_addlinkedserver
            @server     = @LinkedServerName,
            @srvproduct = N'',
            @provider   = @Provider,
            @datasrc    = @DataSource;
    END

    -- Mapear login (use with caution: credenciales en texto plano)
    PRINT 'Configurando mapeo de login...';
    EXEC master.dbo.sp_addlinkedsrvlogin
        @rmtsrvname  = @LinkedServerName,
        @useself     = N'False',
        @locallogin  = NULL,
        @rmtuser     = @RemoteUser,
        @rmtpassword = @RemotePassword;

    -- Opciones recomendadas (ajustar según necesidades)
    PRINT 'Aplicando opciones recomendadas de servidor...';
    EXEC master.dbo.sp_serveroption @server = @LinkedServerName, @optname = N'data access',      @optvalue = N'true';
    EXEC master.dbo.sp_serveroption @server = @LinkedServerName, @optname = N'rpc',              @optvalue = N'true';
    EXEC master.dbo.sp_serveroption @server = @LinkedServerName, @optname = N'rpc out',          @optvalue = N'true';
    EXEC master.dbo.sp_serveroption @server = @LinkedServerName, @optname = N'collation compatible', @optvalue = N'false';
    EXEC master.dbo.sp_serveroption @server = @LinkedServerName, @optname = N'connect timeout',   @optvalue = N'10';
    EXEC master.dbo.sp_serveroption @server = @LinkedServerName, @optname = N'query timeout',     @optvalue = N'60';

    PRINT 'Probando conectividad del linked server...';
    EXEC master.dbo.sp_testlinkedserver @server = @LinkedServerName;

    -- Validaciones de ejemplo (no destructivas)
    PRINT 'Validando existencia de bases y tablas (ejemplos).';

    PRINT 'Base local actual:';
    SELECT DB_NAME() AS BaseActual;

    PRINT '¿Existe la base local especificada?';
    SELECT name AS BaseLocal FROM sys.databases WHERE name = @LocalDatabase;

    PRINT 'Listado de tablas en la base local (TOP 5)';
    USE [GBB_Host];
    SELECT TOP (5) name AS TablaLocal FROM sys.tables ORDER BY name;

    -- Consulta remota de ejemplo (ajustar nombres de tabla/columnas según esquema remoto)
    DECLARE @sql NVARCHAR(MAX) = N'SELECT TOP (5) UsuarioId, Tipo, Nombre, Email, Estado FROM [' + @LinkedServerName + '].[' + @RemoteDatabase + '].[dbo].[Usuarios];';
    PRINT 'Ejecutando consulta remota de ejemplo (mostrar TOP 5 Usuarios)...';
    EXEC sp_executesql @sql;

    PRINT 'Consulta cruzada de ejemplo (TOP 1 usuarios activos)...';
    DECLARE @sql2 NVARCHAR(MAX) = N'SELECT TOP (1) u.UsuarioId, u.Email, u.Tipo FROM [' + @LinkedServerName + '].[' + @RemoteDatabase + '].[dbo].[Usuarios] u WHERE u.Estado = 1 ORDER BY u.UsuarioId DESC;';
    EXEC sp_executesql @sql2;

    PRINT 'Configuración completada correctamente.';

END TRY
BEGIN CATCH
    PRINT 'Ocurrió un error durante la configuración del linked server.';
    SELECT
        ERROR_NUMBER()   AS ErrorNumber,
        ERROR_SEVERITY() AS Severity,
        ERROR_STATE()    AS State,
        ERROR_PROCEDURE()AS ProcedureName,
        ERROR_LINE()     AS ErrorLine,
        ERROR_MESSAGE()  AS ErrorMessage;
    THROW; -- re-lanzar error para que el agente de despliegue lo vea
END CATCH;
GO

/*
  Notas/Mejoras recomendadas:
  - En producción, use credenciales seguras (Azure Key Vault, Credential Store) en lugar de variables con contraseña.
  - Si el proveedor `MSOLEDBSQL` no está instalado, pruebe con `SQLNCLI11` o el proveedor OLE DB apropiado.
  - Ajuste `@DropIfExists` a 0 si no quiere eliminar configuraciones existentes.
  - Para pruebas adicionales, revise los tiempos de espera y opciones RPC según el rendimiento.
*/

