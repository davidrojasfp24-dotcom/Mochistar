// Función para pintar el historial de logs de auditoría en la pantalla
async function cargarSeccionLogs() {
    const contenedor = document.getElementById('contenedor-principal');
    
    // Dibujamos la estructura básica de la sección de Logs
    contenedor.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white">Historial de Logs (Auditoría)</h2>
            <button class="btn btn-outline-light btn-sm" onclick="cargarSeccionLogs()">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
            </button>
        </div>
        <div class="table-responsive bg-dark p-3 rounded shadow">
            <table class="table table-dark table-hover align-middle border-secondary">
                <thead>
                    <tr>
                        <th>ID LOG</th>
                        <th>ID USUARIO</th>
                        <th>MENSAJE</th>
                        <th>FECHA/HORA</th>
                    </tr>
                </thead>
                <tbody id="tabla-logs-body">
                    <tr><td colspan="4" class="text-center p-4">Cargando historial de logs...</td></tr>
                </tbody>
            </table>
        </div>`;

    try {
        // Pedimos los logs a la API
        const response = await fetch('index.php?controller=api&action=logs');
        const result = await response.json();
        const tbody = document.getElementById('tabla-logs-body');
        
        console.log("Logs recibidos:", result);

        if (result.estado === 'Exito' && result.data) {
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-muted">No se registran logs todavía.</td></tr>';
                return;
            }
            
            tbody.innerHTML = result.data.map(l => {
                return `
                    <tr>
                        <td>#${l.log_id}</td>
                        <td>Usuario #${l.usuario_id}</td>
                        <td>${l.mensaje}</td>
                        <td class="text-muted"><small>${l.fecha}</small></td>
                    </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center p-4 text-warning">${result.mensaje || 'Error al obtener los registros.'}</td></tr>`;
        }
    } catch (error) {
        console.error("Error en log fetch:", error);
        document.getElementById('tabla-logs-body').innerHTML = '<tr><td colspan="4" class="text-center text-danger p-4">Error de conexión al cargar logs.</td></tr>';
    }
}