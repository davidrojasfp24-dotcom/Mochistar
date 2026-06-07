/**
 * Gestor de la sección de Pedidos (hereda de BaseManager)
 */
class PedidoManager extends BaseManager {
    constructor(contenedorId) {
        super(contenedorId);
        this.setupEventHandlers();
    }

    /**
     * Asocia manejadores de eventos usando delegación en el contenedor principal
     */
    setupEventHandlers() {
        this.contenedor.addEventListener('click', (event) => {
            if (adminController.currentSection !== 'pedido') return;

            const btnActualizar = event.target.closest('[data-action="actualizar-pedidos"]');
            if (btnActualizar) {
                this.cargar();
                return;
            }

            const btnVerDetalle = event.target.closest('[data-action="ver-detalle-pedido"]');
            if (btnVerDetalle) {
                const id = btnVerDetalle.getAttribute('data-id');
                this.verDetalle(id);
                return;
            }
        });

        // Event listener para el selector de cambiar estado usando delegación y change listener
        this.contenedor.addEventListener('change', (event) => {
            if (adminController.currentSection !== 'pedido') return;

            const selectEstado = event.target.closest('[data-action="cambiar-estado-pedido"]');
            if (selectEstado) {
                const id = selectEstado.getAttribute('data-id');
                const nuevoEstado = selectEstado.value;
                this.actualizarEstado(id, nuevoEstado, selectEstado);
            }
        });
    }

    /**
     * Carga todos los pedidos registrados en el sistema (Async/Await)
     */
    async cargar() {
        this.mostrarCargando("Cargando lista de pedidos...");

        try {
            const response = await fetch('index.php?controller=api&action=pedidos');
            const json = await response.json();

            if (json.estado !== 'Exito') {
                this.contenedor.innerHTML = `
                    <div class="alert alert-warning border-0 shadow-sm">
                        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Aviso</h4>
                        <p>${json.mensaje || 'Error al cargar los datos.'}</p>
                        <hr>
                        <button class="btn btn-warning btn-sm" data-action="actualizar-pedidos">Reintentar</button>
                    </div>`;
                return;
            }

            const pedidos = json.data;

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="text-white mb-0"><i class="bi bi-cart-check me-2 text-info"></i>Gestión de Pedidos</h2>
                        <small class="text-muted">${pedidos.length} pedido(s) registrado(s)</small>
                    </div>
                    <button class="btn btn-sm btn-outline-info" data-action="actualizar-pedidos">
                        <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                    </button>
                </div>

                <div class="table-responsive shadow-sm bg-dark p-3 rounded">
                    <table class="table table-dark table-hover align-middle border-secondary mb-0">
                        <thead class="border-secondary">
                            <tr class="text-uppercase text-muted small">
                                <th>#ID</th>
                                <th>FECHA</th>
                                <th>CLIENTE</th>
                                <th>TOTAL</th>
                                <th>ESTADO</th>
                                <th class="text-center">CAMBIAR ESTADO</th>
                                <th class="text-center">DETALLE</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-pedidos-body">`;

            if (pedidos.length === 0) {
                html += `<tr><td colspan="7" class="text-center text-muted p-4">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay pedidos registrados.
                         </td></tr>`;
            } else {
                pedidos.forEach(({ id_pedido, estado, fecha, precio, id_usuario, nombre_usuario, email }) => {
                    let badgeClass = 'bg-secondary';
                    let badgeIcon  = 'bi-circle';
                    if (estado === 'Pendiente')  { badgeClass = 'bg-warning text-dark'; badgeIcon = 'bi-clock'; }
                    if (estado === 'Enviado')     { badgeClass = 'bg-info text-dark';    badgeIcon = 'bi-truck'; }
                    if (estado === 'Completado')  { badgeClass = 'bg-success';           badgeIcon = 'bi-check-circle'; }
                    if (estado === 'Cancelado')   { badgeClass = 'bg-danger';            badgeIcon = 'bi-x-circle'; }

                    const cliente = nombre_usuario
                        ? `<span class="fw-semibold text-white">${nombre_usuario}</span>
                           <br><small class="text-muted">${email || ''}</small>`
                        : `<span class="text-muted">ID: ${id_usuario}</span>`;

                    const fechaFormateada = fecha
                        ? new Date(fecha).toLocaleString('es-ES', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' })
                        : '—';

                    html += `
                        <tr>
                            <td class="fw-bold text-info">#${id_pedido}</td>
                            <td class="small text-muted">${fechaFormateada}</td>
                            <td>${cliente}</td>
                            <td class="fw-bold text-success">${parseFloat(precio).toFixed(2)} €</td>
                            <td>
                                <span class="badge ${badgeClass} rounded-pill px-2">
                                    <i class="bi ${badgeIcon} me-1"></i>${estado}
                                </span>
                            </td>
                            <td class="text-center" style="min-width:175px;">
                                <select class="form-select form-select-sm bg-dark text-white border-secondary"
                                        data-action="cambiar-estado-pedido" data-id="${id_pedido}">
                                    <option value="" disabled selected>Cambiar...</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Enviado">Enviado</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-action="ver-detalle-pedido" data-id="${id_pedido}"
                                        title="Ver productos del pedido">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            }

            html += `</tbody></table></div>`;
            this.contenedor.innerHTML = html;

        } catch (error) {
            console.error("Error JS al cargar pedidos:", error);
            this.mostrarError("No se pudo conectar con la API de pedidos.");
        }
    }

    /**
     * Modifica el estado de un pedido (Async/Await)
     */
    async actualizarEstado(id, nuevoEstado, selectEl) {
        if (!confirm(`¿Confirmas el cambio del pedido #${id} a "${nuevoEstado}"?`)) {
            selectEl.value = '';
            return;
        }

        selectEl.disabled = true;

        try {
            const response = await fetch('index.php?controller=api&action=pedidos', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_pedido: id, nuevo_estado: nuevoEstado })
            });

            const res = await response.json();

            if (res.estado === 'Exito') {
                this.cargar();
            } else {
                alert('Error: ' + res.mensaje);
                selectEl.disabled = false;
                selectEl.value = '';
            }
        } catch (e) {
            console.error("Error al actualizar estado pedido:", e);
            alert("Error de conexión al servidor.");
            selectEl.disabled = false;
            selectEl.value = '';
        }
    }

    /**
     * Muestra el detalle modal del pedido con su desglose de productos
     */
    async verDetalle(id) {
        const modalEl = document.getElementById('modalDetallePedido');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        document.getElementById('modalDetallePedidoTitulo').innerHTML = `<i class="bi bi-receipt me-2 text-info"></i>Detalle del Pedido #${id}`;
        document.getElementById('modalDetallePedidoBody').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-info" role="status"></div>
                <p class="text-muted mt-2">Cargando líneas del pedido...</p>
            </div>`;
        modal.show();

        try {
            const response = await fetch(`index.php?controller=api&action=pedidos&id=${id}`);
            const json = await response.json();

            if (json.estado !== 'Exito' || !json.data) {
                document.getElementById('modalDetallePedidoBody').innerHTML =
                    `<div class="alert alert-warning mb-0">No se pudieron cargar los datos del pedido.</div>`;
                return;
            }

            // Desestructuración del resultado JSON (JS Avanzado)
            const { nombre_usuario, id_usuario, fecha, precio, lineas } = json.data;

            let bodyHtml = `
                <div class="mb-3 d-flex gap-3 flex-wrap">
                    <span class="badge bg-secondary fs-6"><i class="bi bi-person me-1"></i>${nombre_usuario || 'ID ' + id_usuario}</span>
                    <span class="badge bg-dark border border-secondary fs-6"><i class="bi bi-calendar me-1"></i>${new Date(fecha).toLocaleString('es-ES')}</span>
                    <span class="badge bg-success fs-6"><i class="bi bi-currency-euro me-1"></i>${parseFloat(precio).toFixed(2)} €</span>
                </div>
                <h6 class="text-muted text-uppercase small mb-2">Productos del pedido</h6>`;

            if (!lineas || lineas.length === 0) {
                bodyHtml += `<p class="text-muted fst-italic">Este pedido no tiene líneas registradas.</p>`;
            } else {
                bodyHtml += `<div class="table-responsive">
                    <table class="table table-dark table-sm align-middle border-secondary mb-0">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio/ud</th>
                                <th class="text-end">Descuento</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;

                lineas.forEach(({ nombre_producto, cantidad, precio_unidad, porcentaje_descuento }) => {
                    const subtotal = parseFloat(precio_unidad) * parseInt(cantidad);
                    const descuento = porcentaje_descuento ? parseFloat(porcentaje_descuento).toFixed(0) + '%' : '—';
                    bodyHtml += `
                        <tr>
                            <td class="fw-semibold">${nombre_producto || 'Producto desconocido'}</td>
                            <td class="text-center">${cantidad}</td>
                            <td class="text-end">${parseFloat(precio_unidad).toFixed(2)} €</td>
                            <td class="text-end text-warning">${descuento}</td>
                            <td class="text-end fw-bold text-success">${subtotal.toFixed(2)} €</td>
                        </tr>`;
                });

                bodyHtml += `</tbody></table></div>`;
            }

            document.getElementById('modalDetallePedidoBody').innerHTML = bodyHtml;

        } catch (err) {
            console.error("Error al cargar detalle pedido:", err);
            document.getElementById('modalDetallePedidoBody').innerHTML =
                `<div class="alert alert-danger mb-0">Error de conexión al cargar el detalle.</div>`;
        }
    }
}