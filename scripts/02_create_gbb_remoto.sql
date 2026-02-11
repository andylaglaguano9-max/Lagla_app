/*
  02_create_gbb_remoto.sql
  Script de creacion de estructura para GBB_Remoto
  Basado en el esquema real compartido por el usuario.
*/

IF DB_ID(N'GBB_Remoto') IS NULL
BEGIN
    CREATE DATABASE [GBB_Remoto];
END
GO

USE [GBB_Remoto];
GO

CREATE TABLE dbo.Usuarios (
    UsuarioId INT IDENTITY(1,1) NOT NULL,
    Tipo VARCHAR(20) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Nombre VARCHAR(80) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Email VARCHAR(120) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Usuarios_Estado DEFAULT ((1)),
    PasswordHash VARCHAR(255) COLLATE Modern_Spanish_CI_AS NULL,
    Telefono VARCHAR(20) COLLATE Modern_Spanish_CI_AS NULL,
    CONSTRAINT PK_Usuarios PRIMARY KEY CLUSTERED (UsuarioId)
);
GO

CREATE TABLE dbo.Plataformas (
    PlataformaId INT IDENTITY(1,1) NOT NULL,
    Nombre VARCHAR(50) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Plataformas_Estado DEFAULT ((1)),
    CONSTRAINT PK_Plataformas PRIMARY KEY CLUSTERED (PlataformaId)
);
GO

CREATE TABLE dbo.Juegos (
    JuegoId INT IDENTITY(1,1) NOT NULL,
    PlataformaId INT NOT NULL,
    Nombre VARCHAR(120) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Juegos_Estado DEFAULT ((1)),
    Descripcion NVARCHAR(300) COLLATE Modern_Spanish_CI_AS NULL,
    ImagenUrl NVARCHAR(300) COLLATE Modern_Spanish_CI_AS NULL,
    Precio DECIMAL(10,2) NOT NULL CONSTRAINT DF__Juegos__Precio__5CD6CB2B DEFAULT ((9.99)),
    Genero VARCHAR(50) COLLATE Modern_Spanish_CI_AS NULL,
    CONSTRAINT PK_Juegos PRIMARY KEY CLUSTERED (JuegoId)
);
GO

CREATE TABLE dbo.KeysInventario (
    KeyId INT IDENTITY(1,1) NOT NULL,
    JuegoId INT NOT NULL,
    VendedorId INT NOT NULL,
    KeyValor VARCHAR(80) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    Estado VARCHAR(20) COLLATE Modern_Spanish_CI_AS NOT NULL,
    FechaPublicacion DATETIME2(0) NOT NULL CONSTRAINT DF_Keys_FechaPub DEFAULT (sysutcdatetime()),
    OrdenIdHost INT NULL,
    FechaVenta DATETIME2(0) NULL,
    CONSTRAINT PK_KeysInventario PRIMARY KEY CLUSTERED (KeyId),
    CONSTRAINT UQ_KeysInventario_KeyValor UNIQUE NONCLUSTERED (KeyValor)
);
GO

CREATE TABLE dbo.Auditoria (
    AuditoriaId INT IDENTITY(1,1) NOT NULL,
    UsuarioId INT NULL,
    Accion VARCHAR(100) COLLATE Modern_Spanish_CI_AS NULL,
    Modulo VARCHAR(50) COLLATE Modern_Spanish_CI_AS NULL,
    Detalle VARCHAR(255) COLLATE Modern_Spanish_CI_AS NULL,
    FechaHora DATETIME NULL CONSTRAINT DF__Auditoria__Fecha__01142BA1 DEFAULT (getdate()),
    CONSTRAINT PK__Auditori__095694C3BCC41C8F PRIMARY KEY CLUSTERED (AuditoriaId)
);
GO

CREATE TABLE dbo.GameKeys (
    KeyId INT IDENTITY(1,1) NOT NULL,
    JuegoId INT NULL,
    Clave VARCHAR(200) COLLATE Modern_Spanish_CI_AS NULL,
    Vendida BIT NULL CONSTRAINT DF__GameKeys__Vendid__17036CC0 DEFAULT ((0)),
    CONSTRAINT PK__GameKeys__21F5BE471E700788 PRIMARY KEY CLUSTERED (KeyId)
);
GO

CREATE TABLE dbo.ParametrosSistema (
    Parametro VARCHAR(50) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Valor VARCHAR(100) COLLATE Modern_Spanish_CI_AS NOT NULL,
    Descripcion VARCHAR(200) COLLATE Modern_Spanish_CI_AS NULL,
    CONSTRAINT PK__Parametr__4928AC4E2FC832B1 PRIMARY KEY CLUSTERED (Parametro)
);
GO

CREATE TABLE dbo.Temas (
    TemaId INT IDENTITY(1,1) NOT NULL,
    Nombre VARCHAR(50) COLLATE Modern_Spanish_CI_AS NULL,
    Fondo VARCHAR(50) COLLATE Modern_Spanish_CI_AS NULL,
    ColorPrimario VARCHAR(50) COLLATE Modern_Spanish_CI_AS NULL,
    ColorSecundario VARCHAR(50) COLLATE Modern_Spanish_CI_AS NULL,
    Activo BIT NULL,
    CONSTRAINT PK__Temas__BF02E6F668B22452 PRIMARY KEY CLUSTERED (TemaId)
);
GO

ALTER TABLE dbo.Juegos
ADD CONSTRAINT FK_Juegos_Plataformas
FOREIGN KEY (PlataformaId) REFERENCES dbo.Plataformas(PlataformaId);
GO

ALTER TABLE dbo.KeysInventario
ADD CONSTRAINT FK_Keys_Juegos
FOREIGN KEY (JuegoId) REFERENCES dbo.Juegos(JuegoId);
GO

ALTER TABLE dbo.KeysInventario
ADD CONSTRAINT FK_Keys_Usuarios
FOREIGN KEY (VendedorId) REFERENCES dbo.Usuarios(UsuarioId);
GO

CREATE UNIQUE NONCLUSTERED INDEX UQ_Juegos_Nombre
ON dbo.Juegos (Nombre);
GO

CREATE VIEW dbo.vw_CatalogoConStock
AS
SELECT
    j.JuegoId,
    j.Nombre AS Juego,
    p.PlataformaId,
    p.Nombre AS Plataforma,
    COUNT(CASE WHEN k.Estado = 'DISPONIBLE' THEN 1 END) AS StockDisponible,
    MIN(CASE WHEN k.Estado = 'DISPONIBLE' THEN k.Precio END) AS PrecioMin,
    MAX(CASE WHEN k.Estado = 'DISPONIBLE' THEN k.Precio END) AS PrecioMax
FROM dbo.Juegos j
JOIN dbo.Plataformas p ON p.PlataformaId = j.PlataformaId
LEFT JOIN dbo.KeysInventario k ON k.JuegoId = j.JuegoId
WHERE j.Estado = 1
  AND p.Estado = 1
GROUP BY j.JuegoId, j.Nombre, p.PlataformaId, p.Nombre;
GO
