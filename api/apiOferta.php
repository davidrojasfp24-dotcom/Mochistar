<?php
// API REST para gestión de ofertas
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../model/ofertaDAO.php';

// Verificar que el usuario tenga permisos de administrador
verificarAdmin();

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        if (isset($_GET['id'])) {
            obtenerOferta($_GET['id']);
        } else {
            obtenerOfertas();
        }
        break;
    case 'POST':
        crearOferta();
        break;
    case 'PUT':
        actualizarOferta();
        break;
    case 'DELETE':
        eliminarOferta();
        break;
    default:
        respuestaJSON('Fallido', null, 'Método no permitido', 405);
}

// Obtener todas las ofertas
function obtenerOfertas() {
    $ofertas = ofertaDAO::getOfertas();
    $resultado = [];
    foreach ($ofertas as $oferta) {
        $resultado[] = [
            'id_oferta'            => $oferta['id_oferta'] ?? null,
            'tipo_oferta'          => $oferta['tipo_oferta'] ?? null,
            'descripcion'          => $oferta['descripcion'] ?? null,
            'porcentaje_descuento' => $oferta['porcentaje_descuento'] ?? null
        ];
    }
    respuestaJSON('Exito', $resultado);
}

// Obtener una oferta específica
function obtenerOferta($id) {
    $oferta = ofertaDAO::getOfertaByID(intval($id));
    if ($oferta) {
        $resultado = [
            'id_oferta'            => $oferta['id_oferta'] ?? null,
            'tipo_oferta'          => $oferta['tipo_oferta'] ?? null,
            'descripcion'          => $oferta['descripcion'] ?? null,
            'porcentaje_descuento' => $oferta['porcentaje_descuento'] ?? null
        ];
        respuestaJSON('Exito', $resultado);
    } else {
        respuestaJSON('Fallido', null, 'Oferta no encontrada', 404);
    }
}

// Crear una nueva oferta
function crearOferta() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['tipo_oferta']) || !isset($data['porcentaje_descuento'])) {
        respuestaJSON('Fallido', null, 'Datos incompletos: tipo_oferta y porcentaje_descuento son requeridos', 400);
        return;
    }
    $tipo      = trim($data['tipo_oferta']);
    $desc      = trim($data['descripcion'] ?? '');
    $descuento = floatval($data['porcentaje_descuento']);

    $id = ofertaDAO::crearOferta($tipo, $desc, $descuento);
    if ($id) {
        respuestaJSON('Exito', ['id_oferta' => $id], 'Oferta creada correctamente', 201);
    } else {
        respuestaJSON('Fallido', null, 'Error al crear la oferta', 500);
    }
}

// Actualizar una oferta existente
function actualizarOferta() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id_oferta'])) {
        respuestaJSON('Fallido', null, 'ID de oferta requerido', 400);
        return;
    }
    $id        = intval($data['id_oferta']);
    $tipo      = trim($data['tipo_oferta'] ?? '');
    $desc      = trim($data['descripcion'] ?? '');
    $descuento = floatval($data['porcentaje_descuento'] ?? 0);

    $affected = ofertaDAO::actualizarOferta($id, $tipo, $desc, $descuento);
    if ($affected !== false) {
        respuestaJSON('Exito', null, 'Oferta actualizada correctamente');
    } else {
        respuestaJSON('Fallido', null, 'Error al actualizar la oferta', 500);
    }
}

// Eliminar una oferta
function eliminarOferta() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id_oferta'])) {
        respuestaJSON('Fallido', null, 'ID de oferta requerido', 400);
        return;
    }
    $id = intval($data['id_oferta']);

    $affected = ofertaDAO::eliminarOferta($id);
    if ($affected > 0) {
        respuestaJSON('Exito', null, 'Oferta eliminada correctamente');
    } else {
        respuestaJSON('Fallido', null, 'No se encontró la oferta o no se pudo eliminar', 404);
    }
}
?>
