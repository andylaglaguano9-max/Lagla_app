<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../../models/ConfiguracionModel.php';
require_once __DIR__ . '/../../models/AuditoriaModel.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (($_POST['action'] ?? '') === 'param_update') {
            $parametro = (string)($_POST['parametro'] ?? '');
            $valor = (string)($_POST['valor'] ?? '');
            ConfiguracionModel::actualizarParametro($parametro, $valor);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'ACTUALIZAR',
                    'Configuracion',
                    "Parámetro: {$parametro}"
                );
            } catch (Exception $e) {
            }
            $success = 'Parámetro actualizado';
        } elseif (($_POST['action'] ?? '') === 'plataforma_create') {
            $nombre = (string)($_POST['nombre'] ?? '');
            ConfiguracionModel::crearPlataforma($nombre, isset($_POST['estado']) ? 1 : 0);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'CREAR',
                    'Plataformas',
                    "Plataforma creada: {$nombre}"
                );
            } catch (Exception $e) {
            }
            $success = 'Plataforma creada';
        } elseif (($_POST['action'] ?? '') === 'plataforma_update') {
            $plataformaId = (int)($_POST['plataformaId'] ?? 0);
            $nombre = (string)($_POST['nombre'] ?? '');
            ConfiguracionModel::actualizarPlataforma($plataformaId, $nombre, isset($_POST['estado']) ? 1 : 0);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'EDITAR',
                    'Plataformas',
                    "Plataforma actualizada: {$plataformaId}"
                );
            } catch (Exception $e) {
            }
            $success = 'Plataforma actualizada';
        } elseif (($_POST['action'] ?? '') === 'tema_activar') {
            $temaId = (int)($_POST['temaId'] ?? 0);
            ConfiguracionModel::activarTema($temaId);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'ACTIVAR',
                    'Temas',
                    "Tema activado: {$temaId}"
                );
            } catch (Exception $e) {
            }
            $success = 'Tema activado';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$parametros = [];
$plataformas = [];
$temas = [];
try {
    $parametros = ConfiguracionModel::listarParametros();
    $plataformas = ConfiguracionModel::listarPlataformas();
    $temas = ConfiguracionModel::listarTemas();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/admin/configuracion.php';
