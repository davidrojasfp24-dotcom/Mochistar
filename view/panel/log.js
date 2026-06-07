/**
 * Gestor de la sección de Auditoría de Logs (hereda de BaseManager)
 */
class LogManager extends BaseManager {
    constructor(contenedorId) {
        super(contenedorId);
        this.setupEventHandlers();
    }

    /**
     * Asocia manejadores de eventos usando delegación en el contenedor principal
     */
    setupEventHandlers() {
        // Delegación de eventos en el contenedor principal (JS Avanzado)
        this.contenedor.addEventListener('click', (event) => {
            if (adminController.currentSection !== 'log') return;

            const btnActualizar = event.target.closest('[data-action="actualizar-logs"]');
            if (btnActualizar) {
                this.cargar();
                return;
            }
        });
    }

    /**
     * Carga el historial completo de logs de la aplicación (Async/Await)
     */
    async cargar() {
        this.mostrarCargando("Cargando historial de auditoría...");

        this.contenedor.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white">Historial de Logs (Auditoría)</h2>
                <button class="btn btn-outline-light btn-sm" data-action="actualizar-logs">
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
            const response = await fetch('index.php?controller=api&action=logs');
            const result = await response.json();
            const tbody = document.getElementById('tabla-logs-body');

            if (result.estado === 'Exito' && result.data) {
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-muted">No se registran logs todavía.</td></tr>';
                    return;
                }
                
                // Desestructuración de propiedades (JS Avanzado)
                tbody.innerHTML = result.data.map(({ log_id, usuario_id, mensaje, fecha }) => `
                    <tr>
                        <td>#${log_id}</td>
                        <td>Usuario #${usuario_id}</td>
                        <td>${mensaje}</td>
                        <td class="text-muted"><small>${fecha}</small></td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center p-4 text-warning">${result.mensaje || 'Error al obtener los registros.'}</td></tr>`;
            }
        } catch (error) {
            console.error("Error en log fetch:", error);
            this.mostrarError("Error de conexión al cargar logs.");
        }
    }
}