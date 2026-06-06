// =============================================
// SECCIÓN: GESTIÓN DE PEDIDOS (Panel Admin)
// =============================================

async function cargarSeccionPedidos() {
    const contenedor = document.getElementById('contenedor-principal');

    contenedor.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Cargando pedidos...</p>
        </div>`;

    try {
        const response = await fetch('index.php?controller=api&action=pedidos');
        const json = await response.json();

        if (json.estado !== 'Exito') {
            contenedor.innerHTML = `
                <div class="alert alert-warning border-0 shadow-sm">
                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Aviso</h4>
                    <p>${json.mensaje || 'Error al cargar los datos.'}</p>
                    <hr>
                    <button class="btn btn-warning btn-sm" onclick="cargarSeccionPedidos()">Reintentar</button>
                </div>`;
            return;
        }

        const pedidos = json.data;

        // ── Cabecera con título y botón de refresco ──
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0"><i class="bi bi-cart-check me-2 text-info"></i>Gestión de Pedidos</h2>
                    <small class="text-muted">${pedidos.length} pedido(s) registrado(s)</small>
                </div>
                <button class="btn btn-sm btn-outline-info" onclick="cargarSeccionPedidos()">
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
                    <tbody>`;

        if (pedidos.length === 0) {
            html += `<tr><td colspan="7" class="text-center text-muted p-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay pedidos registrados.
                     </td></tr>`;
        } else {
            pedidos.forEach(p => {
                // Badge de color según estado
                let badgeClass = 'bg-secondary';
                let badgeIcon  = 'bi-circle';
                if (p.estado === 'Pendiente')  { badgeClass = 'bg-warning text-dark'; badgeIcon = 'bi-clock'; }
                if (p.estado === 'Enviado')     { badgeClass = 'bg-info text-dark';    badgeIcon = 'bi-truck'; }
                if (p.estado === 'Completado')  { badgeClass = 'bg-success';           badgeIcon = 'bi-check-circle'; }
                if (p.estado === 'Cancelado')   { badgeClass = 'bg-danger';            badgeIcon = 'bi-x-circle'; }

                // Nombre del cliente (viene del JOIN) o fallback al ID
                const cliente = p.nombre_usuario
                    ? `<span class="fw-semibold text-white">${p.nombre_usuario}</span>
                       <br><small class="text-muted">${p.email || ''}</small>`
                    : `<span class="text-muted">ID: ${p.id_usuario}</span>`;

                // Fecha formateada
                const fecha = p.fecha
                    ? new Date(p.fecha).toLocaleString('es-ES', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' })
                    : '—';

                html += `
                    <tr>
                        <td class="fw-bold text-info">#${p.id_pedido}</td>
                        <td class="small text-muted">${fecha}</td>
                        <td>${cliente}</td>
                        <td class="fw-bold text-success">${parseFloat(p.precio).toFixed(2)} €</td>
                        <td>
                            <span class="badge ${badgeClass} rounded-pill px-2">
                                <i class="bi ${badgeIcon} me-1"></i>${p.estado}
                            </span>
                        </td>
                        <td class="text-center" style="min-width:175px;">
                            <select class="form-select form-select-sm bg-dark text-white border-secondary"
                                    onchange="actualizarEstadoPedido(${p.id_pedido}, this.value, this)">
                                <option value="" disabled selected>Cambiar...</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Enviado">Enviado</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="verDetallePedido(${p.id_pedido})"
                                    title="Ver productos del pedido">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>`;
            });
        }

        html += `</tbody></table></div>`;
        contenedor.innerHTML = html;

    } catch (error) {
        console.error("Error JS:", error);
        contenedor.innerHTML = `
            <div class="alert alert-danger border-0 bg-dark text-white border-danger mt-4">
                <i class="bi bi-x-circle me-2"></i>
                <strong>Error Crítico:</strong> No se pudo conectar con la API de pedidos.
            </div>`;
    }
}

// ── Actualizar estado de un pedido (PUT) ──
async function actualizarEstadoPedido(id, nuevoEstado, selectEl) {
    if (!confirm(`¿Confirmas el cambio del pedido #${id} a "${nuevoEstado}"?`)) {
        // Restablecemos el select a su estado original
        selectEl.value = '';
        return;
    }

    // Desactivamos el select mientras guardamos
    selectEl.disabled = true;

    try {
        const response = await fetch('index.php?controller=api&action=pedidos', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_pedido: id, nuevo_estado: nuevoEstado })
        });

        const res = await response.json();

        if (res.estado === 'Exito') {
            // Recargamos la tabla entera para reflejar el nuevo badge de estado
            cargarSeccionPedidos();
        } else {
            alert('Error: ' + res.mensaje);
            selectEl.disabled = false;
            selectEl.value = '';
        }
    } catch (e) {
        console.error("Error al actualizar:", e);
        alert("Error de conexión al servidor.");
        selectEl.disabled = false;
        selectEl.value = '';
    }
}

// ── Ver detalle (líneas) de un pedido ──
async function verDetallePedido(id) {
    // Abrimos el modal y mostramos spinner mientras cargamos
    const modalEl = document.getElementById('modalDetallePedido');
    // Usamos getInstance primero para no duplicar instancias, o creamos una nueva
    const modal   = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    document.getElementById('modalDetallePedidoTitulo').textContent = `Detalle del Pedido #${id}`;
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

        const pedido = json.data;
        const lineas = pedido.lineas || [];

        let bodyHtml = `
            <div class="mb-3 d-flex gap-3 flex-wrap">
                <span class="badge bg-secondary fs-6"><i class="bi bi-person me-1"></i>${pedido.nombre_usuario || 'ID ' + pedido.id_usuario}</span>
                <span class="badge bg-dark border border-secondary fs-6"><i class="bi bi-calendar me-1"></i>${new Date(pedido.fecha).toLocaleString('es-ES')}</span>
                <span class="badge bg-success fs-6"><i class="bi bi-currency-euro me-1"></i>${parseFloat(pedido.precio).toFixed(2)} €</span>
            </div>
            <h6 class="text-muted text-uppercase small mb-2">Productos del pedido</h6>`;

        if (lineas.length === 0) {
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

            lineas.forEach(l => {
                const subtotal   = parseFloat(l.precio_unidad) * parseInt(l.cantidad);
                const descuento  = l.porcentaje_descuento ? parseFloat(l.porcentaje_descuento).toFixed(0) + '%' : '—';
                bodyHtml += `
                    <tr>
                        <td class="fw-semibold">${l.nombre_producto || 'Producto desconocido'}</td>
                        <td class="text-center">${l.cantidad}</td>
                        <td class="text-end">${parseFloat(l.precio_unidad).toFixed(2)} €</td>
                        <td class="text-end text-warning">${descuento}</td>
                        <td class="text-end fw-bold text-success">${subtotal.toFixed(2)} €</td>
                    </tr>`;
            });

            bodyHtml += `</tbody></table></div>`;
        }

        document.getElementById('modalDetallePedidoBody').innerHTML = bodyHtml;

    } catch (err) {
        console.error("Error al cargar detalle:", err);
        document.getElementById('modalDetallePedidoBody').innerHTML =
            `<div class="alert alert-danger mb-0">Error de conexión al cargar el detalle.</div>`;
    }
}