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

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white"><i class="bi bi-cart-check me-2"></i>Gestión de Pedidos</h2>
                <button class="btn btn-sm btn-outline-info" onclick="cargarSeccionPedidos()">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
            <div class="table-responsive shadow-sm bg-dark p-3 rounded">
                <table class="table table-dark table-hover align-middle border-secondary">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>FECHA</th>
                            <th>USUARIO</th>
                            <th>TOTAL</th>
                            <th>ESTADO</th>
                            <th class="text-end">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>`;

        if (pedidos.length === 0) {
            html += `<tr><td colspan="6" class="text-center text-muted p-4">No hay pedidos registrados.</td></tr>`;
        } else {
            pedidos.forEach(p => {
                let badgeClass = 'bg-secondary';
                if (p.estado === 'Pendiente') badgeClass = 'bg-warning text-dark';
                if (p.estado === 'Enviado') badgeClass = 'bg-info';
                if (p.estado === 'Completado') badgeClass = 'bg-success';
                if (p.estado === 'Cancelado') badgeClass = 'bg-danger';

                html += `
                    <tr>
                        <td class="fw-bold text-info">#${p.id_pedido}</td>
                        <td class="small text-muted">${p.fecha}</td>
                        <td><span class="text-muted">ID Usuario:</span> ${p.id_usuario}</td>
                        <td class="fw-bold">${parseFloat(p.precio).toFixed(2)} €</td>
                        <td><span class="badge ${badgeClass}">${p.estado}</span></td>
                        <td class="text-end" style="width: 200px;">
                            <select class="form-select form-select-sm bg-dark text-white border-secondary" 
                                    onchange="actualizarEstadoPedido(${p.id_pedido}, this.value)">
                                <option value="" disabled selected>Cambiar estado...</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Enviado">Enviado</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
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
                <strong>Error Crítico:</strong> No se pudo conectar con la API.
            </div>`;
    }
}

async function actualizarEstadoPedido(id, nuevoEstado) {
    if (!confirm(`¿Confirmas el cambio del pedido #${id} a "${nuevoEstado}"?`)) {
        cargarSeccionPedidos();
        return;
    }

    try {
        const response = await fetch('index.php?controller=api&action=pedidos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id_pedido: id, 
                nuevo_estado: nuevoEstado 
            })
        });
        
        const res = await response.json();
        
        if (res.estado === 'Exito') {
            alert("Éxito: " + res.mensaje);
            cargarSeccionPedidos();
        } else {
            alert("Error: " + res.mensaje);
            cargarSeccionPedidos();
        }
    } catch (e) {
        console.error("Error al actualizar:", e);
        alert("Error de conexión al servidor.");
    }
}