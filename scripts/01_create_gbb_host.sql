/*
  01_create_gbb_host.sql
  Base local (HOST) para proyecto GBB
  Requiere Linked Server [10.26.208.149] configurado para objetos que apuntan a GBB_Remoto.
*/

IF DB_ID(N'GBB_Host') IS NULL
BEGIN
    CREATE DATABASE [GBB_Host];
END
GO

USE [GBB_Host];
GO

/* =========================
   TABLAS
========================= */
IF OBJECT_ID(N'dbo.AuditoriaEventos', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.AuditoriaEventos (
        EventoId BIGINT IDENTITY(1,1) PRIMARY KEY,
        FechaHora DATETIME2 NOT NULL CONSTRAINT DF_AuditoriaEventos_FechaHora DEFAULT SYSDATETIME(),
        UsuarioSQL SYSNAME NOT NULL CONSTRAINT DF_AuditoriaEventos_UsuarioSQL DEFAULT SUSER_SNAME(),
        RolDetectado VARCHAR(50) NULL,
        Accion VARCHAR(50) NOT NULL,
        Entidad VARCHAR(50) NOT NULL,
        IdReferencia VARCHAR(50) NULL,
        Servidor VARCHAR(20) NOT NULL CONSTRAINT DF_AuditoriaEventos_Servidor DEFAULT 'HOST',
        DetalleJSON NVARCHAR(MAX) NULL
    );
END
GO

IF OBJECT_ID(N'dbo.Ordenes', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Ordenes (
        OrdenId INT IDENTITY(1,1) PRIMARY KEY,
        ClienteIdRemoto INT NULL,
        Fecha DATETIME2 NOT NULL CONSTRAINT DF_Ordenes_Fecha DEFAULT SYSDATETIME(),
        Total DECIMAL(10,2) NOT NULL,
        Estado VARCHAR(20) NOT NULL,
        CreadoPorLogin SYSNAME NULL CONSTRAINT DF_Ordenes_CreadoPor DEFAULT ORIGINAL_LOGIN(),
        TipoComprador VARCHAR(20) NULL,
        CompradorEmail VARCHAR(150) NULL,
        AnonimoKey VARCHAR(80) NULL,
        FechaRegistroAnonimo DATETIME2 NULL
    );
END
GO

IF OBJECT_ID(N'dbo.DetalleOrden', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.DetalleOrden (
        DetalleId INT IDENTITY(1,1) PRIMARY KEY,
        OrdenId INT NOT NULL,
        JuegoIdRemoto INT NOT NULL,
        PrecioUnitario DECIMAL(10,2) NOT NULL,
        Cantidad INT NOT NULL CONSTRAINT DF_DetalleOrden_Cantidad DEFAULT 1,
        Subtotal AS (PrecioUnitario * Cantidad) PERSISTED,
        GameKey VARCHAR(120) NULL,
        CONSTRAINT FK_DetalleOrden_Ordenes FOREIGN KEY (OrdenId) REFERENCES dbo.Ordenes(OrdenId)
    );
END
GO

IF OBJECT_ID(N'dbo.Entregas', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Entregas (
        EntregaId INT IDENTITY(1,1) PRIMARY KEY,
        OrdenId INT NOT NULL,
        DetalleId INT NOT NULL,
        KeyIdRemoto INT NOT NULL,
        KeyEnmascarada VARCHAR(50) NULL,
        FechaEntrega DATETIME2 NOT NULL CONSTRAINT DF_Entregas_Fecha DEFAULT SYSDATETIME(),
        EntregadoPorLogin SYSNAME NULL CONSTRAINT DF_Entregas_EntregadoPor DEFAULT ORIGINAL_LOGIN(),
        CONSTRAINT FK_Entregas_Ordenes FOREIGN KEY (OrdenId) REFERENCES dbo.Ordenes(OrdenId),
        CONSTRAINT FK_Entregas_DetalleOrden FOREIGN KEY (DetalleId) REFERENCES dbo.DetalleOrden(DetalleId)
    );
END
GO

IF OBJECT_ID(N'dbo.Favoritos', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Favoritos (
        FavoritoId INT IDENTITY(1,1) PRIMARY KEY,
        UsuarioId INT NULL,
        ClienteIdRemoto INT NULL,
        JuegoId INT NOT NULL,
        Fecha DATETIME NOT NULL CONSTRAINT DF_Favoritos_Fecha DEFAULT GETDATE()
    );
    CREATE UNIQUE INDEX UX_Favoritos_Usuario_Juego ON dbo.Favoritos(UsuarioId, JuegoId) WHERE UsuarioId IS NOT NULL;
    CREATE UNIQUE INDEX UX_Favoritos_Cliente_Juego ON dbo.Favoritos(ClienteIdRemoto, JuegoId) WHERE ClienteIdRemoto IS NOT NULL;
END
GO

/* =========================
   FUNCIONES
========================= */
CREATE OR ALTER FUNCTION dbo.fn_DetectRole()
RETURNS VARCHAR(50)
AS
BEGIN
    DECLARE @r VARCHAR(50) = 'SIN_ROL';
    IF IS_ROLEMEMBER('rol_admin') = 1 SET @r = 'rol_admin';
    ELSE IF IS_ROLEMEMBER('rol_auditor') = 1 SET @r = 'rol_auditor';
    ELSE IF IS_ROLEMEMBER('rol_vendedor') = 1 SET @r = 'rol_vendedor';
    ELSE IF IS_ROLEMEMBER('rol_cliente') = 1 SET @r = 'rol_cliente';
    RETURN @r;
END
GO

CREATE OR ALTER FUNCTION dbo.fn_MaskKey(@KeyValor VARCHAR(80))
RETURNS VARCHAR(50)
AS
BEGIN
    DECLARE @len INT = LEN(@KeyValor);
    IF @KeyValor IS NULL OR @len < 4 RETURN 'XXXX-XXXX-XXXX';
    RETURN CONCAT('XXXX-XXXX-', RIGHT(@KeyValor, 4));
END
GO

/* =========================
   SPs CLAVE USADAS POR APP
========================= */
CREATE OR ALTER PROCEDURE dbo.sp_Auditoria_Registrar
    @Accion VARCHAR(50),
    @Entidad VARCHAR(50),
    @IdReferencia VARCHAR(50) = NULL,
    @Servidor VARCHAR(10) = 'HOST',
    @DetalleJSON NVARCHAR(MAX) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.AuditoriaEventos(UsuarioSQL, RolDetectado, Accion, Entidad, IdReferencia, Servidor, DetalleJSON)
    VALUES (SUSER_SNAME(), dbo.fn_DetectRole(), @Accion, @Entidad, @IdReferencia, @Servidor, @DetalleJSON);
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_Auditoria_Listar
AS
BEGIN
    SET NOCOUNT ON;
    SELECT TOP 500 * FROM dbo.AuditoriaEventos ORDER BY FechaHora DESC;
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_Host_CrearOrden
    @ClienteId INT,
    @Total DECIMAL(10,2)
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.Ordenes (ClienteIdRemoto, Total, Estado)
    VALUES (@ClienteId, @Total, 'ABIERTA');

    SELECT SCOPE_IDENTITY() AS OrdenId;
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_Host_InsertarDetalle
    @OrdenId INT,
    @JuegoIdRemoto INT,
    @Precio DECIMAL(10,2)
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.DetalleOrden (OrdenId, JuegoIdRemoto, PrecioUnitario, Cantidad)
    VALUES (@OrdenId, @JuegoIdRemoto, @Precio, 1);

    SELECT SCOPE_IDENTITY() AS DetalleId;
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_Host_RegistrarEntrega
    @OrdenId INT,
    @DetalleId INT,
    @KeyIdRemoto INT
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.Entregas (OrdenId, DetalleId, KeyIdRemoto)
    VALUES (@OrdenId, @DetalleId, @KeyIdRemoto);
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_UI_MisOrdenes
    @ClienteIdRemoto INT
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRY
        SELECT Status = 'OK', Message = 'Ordenes cargadas correctamente';

        SELECT
            o.OrdenId,
            o.Fecha,
            o.Total,
            o.Estado,
            COUNT(d.DetalleId) AS TotalItems
        FROM dbo.Ordenes o
        LEFT JOIN dbo.DetalleOrden d ON d.OrdenId = o.OrdenId
        WHERE o.ClienteIdRemoto = @ClienteIdRemoto
        GROUP BY o.OrdenId, o.Fecha, o.Total, o.Estado
        ORDER BY o.Fecha DESC;
    END TRY
    BEGIN CATCH
        SELECT Status = 'ERROR', Message = ERROR_MESSAGE();
    END CATCH
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_UI_ComprarJuego
    @ClienteIdRemoto INT,
    @JuegoId INT
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @Precio DECIMAL(10,2), @KeyId INT;

        SELECT TOP 1
            @KeyId = KeyId,
            @Precio = Precio
        FROM [10.26.208.149].GBB_Remoto.dbo.KeysInventario
        WHERE JuegoId = @JuegoId AND Estado = 'DISPONIBLE';

        IF @KeyId IS NULL
        BEGIN
            SELECT 'ERROR' AS Status, 'No hay stock disponible' AS Message;
            RETURN;
        END

        INSERT INTO dbo.Ordenes (ClienteIdRemoto, Fecha, Total, Estado)
        VALUES (@ClienteIdRemoto, GETDATE(), @Precio, 'PAGADA');

        DECLARE @OrdenId INT = SCOPE_IDENTITY();

        INSERT INTO dbo.DetalleOrden (OrdenId, JuegoIdRemoto, PrecioUnitario, Cantidad)
        VALUES (@OrdenId, @JuegoId, @Precio, 1);

        DECLARE @DetalleId INT = SCOPE_IDENTITY();

        UPDATE [10.26.208.149].GBB_Remoto.dbo.KeysInventario
        SET Estado = 'VENDIDA', FechaVenta = GETDATE()
        WHERE KeyId = @KeyId;

        INSERT INTO dbo.Entregas (OrdenId, DetalleId, KeyIdRemoto, KeyEnmascarada, FechaEntrega)
        VALUES (
            @OrdenId,
            @DetalleId,
            @KeyId,
            'XXXX-XXXX-' + RIGHT((SELECT KeyValor FROM [10.26.208.149].GBB_Remoto.dbo.KeysInventario WHERE KeyId = @KeyId), 4),
            GETDATE()
        );

        SELECT 'OK' AS Status, 'Compra realizada correctamente' AS Message;
    END TRY
    BEGIN CATCH
        SELECT 'ERROR' AS Status, ERROR_MESSAGE() AS Message;
    END CATCH
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_Catalogo_ListarJuegos
AS
BEGIN
    SET NOCOUNT ON;

    SELECT
        j.JuegoId,
        j.Nombre,
        j.Descripcion,
        j.ImagenUrl,
        j.Precio,
        j.Genero,
        p.Nombre AS Plataforma
    FROM [10.26.208.149].GBB_Remoto.dbo.Juegos j
    INNER JOIN [10.26.208.149].GBB_Remoto.dbo.Plataformas p ON j.PlataformaId = p.PlataformaId
    WHERE j.Estado = 1;
END
GO

CREATE OR ALTER PROCEDURE dbo.sp_LoginInfo
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        SUSER_SNAME() AS UsuarioSQL,
        CAST(CASE WHEN IS_ROLEMEMBER('rol_admin') = 1 THEN 1 ELSE 0 END AS BIT) AS EsAdmin,
        CAST(CASE WHEN IS_ROLEMEMBER('rol_auditor') = 1 THEN 1 ELSE 0 END AS BIT) AS EsAuditor,
        CAST(CASE WHEN IS_ROLEMEMBER('rol_vendedor') = 1 THEN 1 ELSE 0 END AS BIT) AS EsVendedor,
        CAST(CASE WHEN IS_ROLEMEMBER('rol_cliente') = 1 THEN 1 ELSE 0 END AS BIT) AS EsCliente;
END
GO

/* =========================
   VISTAS PRINCIPALES
========================= */
CREATE OR ALTER VIEW dbo.vw_OrdenesConEntregas
AS
SELECT
    o.OrdenId,
    o.Fecha,
    o.Total,
    o.Estado AS EstadoOrden,
    d.DetalleId,
    d.JuegoIdRemoto,
    d.PrecioUnitario,
    d.Cantidad,
    e.EntregaId,
    e.KeyIdRemoto,
    e.KeyEnmascarada,
    e.FechaEntrega
FROM dbo.Ordenes o
JOIN dbo.DetalleOrden d ON d.OrdenId = o.OrdenId
LEFT JOIN dbo.Entregas e ON e.OrdenId = o.OrdenId AND e.DetalleId = d.DetalleId;
GO

CREATE OR ALTER VIEW dbo.vw_AuditoriaDetallada
AS
SELECT
    a.EventoId,
    a.FechaHora,
    a.UsuarioSQL,
    a.RolDetectado,
    a.Accion,
    a.Entidad,
    a.IdReferencia,
    a.Servidor,
    a.DetalleJSON
FROM dbo.AuditoriaEventos a;
GO
