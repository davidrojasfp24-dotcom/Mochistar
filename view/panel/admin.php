<link rel="stylesheet" href="view/panel/admin.css">

<?php
// 1. Iniciamos sesión (si no se ha iniciado ya en el index)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Comprobamos si el usuario existe en la sesión y si su rol es 'admin'
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
    // Si no es admin, lo mandamos al login o a la página principal
    header("Location: index.php?controller=home&action=ver_home");
    exit(); // Detenemos la ejecución para que no cargue el resto del HTML
}
?>

<div class="d-flex" id="wrapper">
    <nav id="sidebar-container" class="bg-dark border-end border-secondary">
        <div class="sidebar-heading text-white-50 p-4">
            <small class="text-uppercase fw-bold">Administración</small>
        </div>
        <div class="list-group list-group-flush px-3">
            <a href="#" onclick="cambiarSeccion('oferta', event)" class="nav-link-custom active">
                <i class="bi bi-percent me-3"></i> Ofertas
            </a>
            <a href="#" onclick="cambiarSeccion('pedido', event)" class="nav-link-custom">
                <i class="bi bi-cart me-3"></i> Pedidos
            </a>
            <a href="#" onclick="cambiarSeccion('producto', event)" class="nav-link-custom">
                <i class="bi bi-box-seam me-3"></i> Productos
            </a>
            <a href="#" onclick="cambiarSeccion('usuario', event)" class="nav-link-custom">
                <i class="bi bi-people me-3"></i> Usuarios
            </a>
            <a href="#" onclick="cambiarSeccion('log', event)" class="nav-link-custom">
                <i class="bi bi-clock-history me-3"></i> Historial (Logs)
            </a>
        </div>
    </nav>

    <main id="page-content-wrapper">
        <div class="container-fluid p-4">
            <div id="contenedor-principal">
                <h2 class="text-white">Panel de Administración Mochistar</h2>
                <p class="text-muted">Bienvenido. Selecciona una opción del menú lateral para gestionar el sistema.</p>
            </div>
        </div>
    </main>
</div>

<script>
    const ADMIN_ACTUAL_ID = <?= $_SESSION['usuario']->getId(); ?>;
</script>

<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="modalTitulo">Datos del Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProducto">
                    <input type="hidden" id="id_producto" name="id_producto">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Nombre del Producto</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Descripción</label>
                        <textarea class="form-control bg-secondary text-white border-0" id="descripcion" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">Precio (€)</label>
                            <input type="number" step="0.01" class="form-control bg-secondary text-white border-0" id="precio_unidad" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">Stock</label>
                            <input type="number" class="form-control bg-secondary text-white border-0" id="cantidad" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Imagen (Nombre del archivo)</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="imagen" placeholder="ej: mochila-pro.png">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="guardarProducto()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="modalTituloUsuario">Datos del Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formUsuario">
                    <input type="hidden" id="id_usuario_form">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nombre</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="u_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Apellido</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="u_apellido">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" class="form-control bg-secondary text-white border-0" id="u_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Rol</label>
                        <select class="form-select bg-secondary text-white border-0" id="u_rol">
                            <option value="cliente">Cliente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3" id="div-contrasena">
                        <label class="form-label text-muted">Contraseña</label>
                        <input type="password" class="form-control bg-secondary text-white border-0" id="u_contrasena">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">Guardar Usuario</button>
            </div>
        </div>
    </div>
</div>

<script src="view/panel/producto.js"></script>
<script src="view/panel/usuario.js"></script>
<script src="view/panel/log.js"></script> 
<script src="view/panel/admin.js"></script>
<script src="view/panel/pedido.js"></script>
<script src="view/panel/oferta.js"></script>