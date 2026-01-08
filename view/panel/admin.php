<link rel="stylesheet" href="view/panel/admin.css">

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
            <a href="#" onclick="cambiarSeccion('valoracion', event)" class="nav-link-custom">
                <i class="bi bi-star me-3"></i> Valoraciones
            </a>
        </div>
    </nav>

    <main id="page-content-wrapper">
        <div class="container-fluid p-4">
            <div id="contenedor-principal">
                <h2 class="text-white">Panel de Mochistar</h2>
                <p class="text-muted">Selecciona una opción a la izquierda.</p>
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
                <h5 class="modal-title" id="modalTitulo">Nuevo Producto</h5>
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
                            <label class="form-label text-muted">Stock Inicial</label>
                            <input type="number" class="form-control bg-secondary text-white border-0" id="cantidad" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Nombre de Imagen (ej: mochila.png)</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="imagen" placeholder="default.png">
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

<script src="view/panel/producto.js"></script>
<script src="view/panel/usuario.js"></script>