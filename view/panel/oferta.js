/**
 * Gestor de la sección de Ofertas (hereda de BaseManager)
 */
class OfertaManager extends BaseManager {
    constructor(contenedorId) {
        super(contenedorId);
        this.modal = null;
        this.form = null;
        this.setupEventHandlers();
    }

    /**
     * Asocia manejadores de eventos usando delegación en el contenedor principal
     */
    setupEventHandlers() {
        // Al cargar la página, se inyecta el modal. Añadimos el listener de guardar:
        document.addEventListener('DOMContentLoaded', () => {
            const btnGuardar = document.getElementById('btn-guardar-oferta');
            if (btnGuardar) {
                btnGuardar.addEventListener('click', () => this.guardar());
            }
        });

        // Delegación de eventos en el contenedor principal (JS Avanzado)
        this.contenedor.addEventListener('click', (event) => {
            if (adminController.currentSection !== 'oferta') return;

            const btnNuevo = event.target.closest('[data-action="nuevo-oferta"]');
            if (btnNuevo) {
                this.abrirModal();
                return;
            }

            const btnEditar = event.target.closest('[data-action="editar-oferta"]');
            if (btnEditar) {
                const id = btnEditar.getAttribute('data-id');
                this.abrirModal(id);
                return;
            }

            const btnEliminar = event.target.closest('[data-action="eliminar-oferta"]');
            if (btnEliminar) {
                const id = btnEliminar.getAttribute('data-id');
                this.eliminar(id);
                return;
            }
        });
    }

    /**
     * Carga todas las ofertas desde la base de datos (Async/Await)
     */
    async cargar() {
        this.mostrarCargando("Cargando ofertas promocionales...");

        this.contenedor.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white"><i class="bi bi-percent me-2"></i>Gestión de Ofertas</h2>
                <button class="btn btn-success btn-sm" data-action="nuevo-oferta">
                    <i class="bi bi-plus-lg me-1"></i> Añadir Oferta
                </button>
            </div>
            <div class="table-responsive bg-dark p-3 rounded shadow">
                <table class="table table-dark table-hover align-middle border-secondary">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>TIPO DE OFERTA</th>
                            <th>DESCRIPCIÓN</th>
                            <th>DESCUENTO</th>
                            <th class="text-end">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-ofertas-body">
                        <tr><td colspan="5" class="text-center p-4">Cargando ofertas...</td></tr>
                    </tbody>
                </table>
            </div>`;

        try {
            const response = await fetch('index.php?controller=api&action=ofertas');
            const res = await response.json();
            const tbody = document.getElementById('tabla-ofertas-body');
            
            const estadoCorrecto = res.estado && res.estado.toLowerCase() === 'exito';

            if (estadoCorrecto && res.data) {
                if (res.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-muted">No hay ofertas registradas en este momento.</td></tr>';
                    return;
                }
                
                // Desestructuración de propiedades en la plantilla (JS Avanzado)
                tbody.innerHTML = res.data.map(({ id_oferta, tipo_oferta, descripcion, porcentaje_descuento }) => `
                    <tr>
                        <td class="text-muted">#${id_oferta}</td>
                        <td><span class="badge bg-info text-dark">${tipo_oferta}</span></td>
                        <td>${descripcion || 'Sin descripción'}</td>
                        <td><span class="text-warning fw-bold">${parseFloat(porcentaje_descuento)}%</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning me-2" data-action="editar-oferta" data-id="${id_oferta}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-action="eliminar-oferta" data-id="${id_oferta}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center p-4 text-warning">${res.mensaje || 'Error al obtener las ofertas.'}</td></tr>`;
            }
        } catch (e) {
            console.error('Error al cargar ofertas:', e);
            this.mostrarError("Error de conexión al cargar ofertas.");
        }
    }

    /**
     * Abre el modal para añadir o editar una oferta
     */
    abrirModal(id = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalOferta'));
        }
        if (!this.form) {
            this.form = document.getElementById('formOferta');
        }
        this.form.reset();
        document.getElementById('id_oferta').value = '';
        
        // Asegurar de enlazar el evento al botón guardar
        const btnGuardar = document.getElementById('btn-guardar-oferta');
        if (btnGuardar && !btnGuardar.dataset.listenerAttached) {
            btnGuardar.addEventListener('click', () => this.guardar());
            btnGuardar.dataset.listenerAttached = 'true';
        }

        if (id) {
            fetch(`index.php?controller=api&action=ofertas&id=${id}`)
                .then(r => r.json())
                .then(res => {
                    const estadoCorrecto = res.estado && res.estado.toLowerCase() === 'exito';
                    if (estadoCorrecto && res.data) {
                        const { id_oferta, tipo_oferta, descripcion, porcentaje_descuento } = res.data;
                        document.getElementById('id_oferta').value = id_oferta;
                        document.getElementById('tipo_oferta').value = tipo_oferta;
                        document.getElementById('descripcion').value = descripcion || '';
                        document.getElementById('porcentaje_descuento').value = porcentaje_descuento;
                        document.getElementById('modalTituloOferta').innerText = 'Editar Oferta';
                        this.modal.show();
                    } else {
                        alert('No se pudo cargar la oferta: ' + (res.mensaje || ''));
                    }
                })
                .catch(err => console.error("Error al obtener detalle:", err));
        } else {
            document.getElementById('modalTituloOferta').innerText = 'Añadir Oferta';
            this.modal.show();
        }
    }

    /**
     * Guarda la oferta (crear o actualizar) (Async/Await)
     */
    async guardar() {
        const id = document.getElementById('id_oferta').value;
        const datos = {
            tipo_oferta: document.getElementById('tipo_oferta').value.trim(),
            descripcion: document.getElementById('descripcion').value.trim(),
            porcentaje_descuento: parseFloat(document.getElementById('porcentaje_descuento').value)
        };
        
        if (id) datos.id_oferta = id;
        const method = id ? 'PUT' : 'POST';
        
        try {
            const response = await fetch('index.php?controller=api&action=ofertas', {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            const res = await response.json();
            const estadoCorrecto = res.estado && res.estado.toLowerCase() === 'exito';
            
            if (estadoCorrecto) {
                this.modal.hide();
                this.cargar();
            } else {
                alert('Error: ' + (res.mensaje || ''));
            }
        } catch (e) {
            console.error("Error al guardar:", e);
            alert("Error de red al guardar la oferta.");
        }
    }

    /**
     * Elimina una oferta por su ID
     */
    async eliminar(id) {
        if (!confirm('¿Eliminar esta oferta?')) return;
        try {
            const response = await fetch('index.php?controller=api&action=ofertas', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_oferta: id })
            });
            const res = await response.json();
            const estadoCorrecto = res.estado && res.estado.toLowerCase() === 'exito';
            
            if (estadoCorrecto) {
                this.cargar();
            } else {
                alert('Error al eliminar: ' + (res.mensaje || ''));
            }
        } catch (e) {
            console.error("Error al eliminar:", e);
            alert("Error de red al eliminar la oferta.");
        }
    }
}

// Inyección del modal al cargar la página (una sola vez)
document.addEventListener('DOMContentLoaded', () => {
    const modalHtml = `
    <div class="modal fade" id="modalOferta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalTituloOferta">Añadir Oferta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formOferta">
                        <input type="hidden" id="id_oferta">
                        <div class="mb-3">
                            <label class="form-label text-muted">Tipo de Oferta</label>
                            <input type="text" class="form-control bg-secondary text-white border-0" id="tipo_oferta" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Descripción</label>
                            <textarea class="form-control bg-secondary text-white border-0" id="descripcion" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Descuento (%)</label>
                            <input type="number" step="0.01" class="form-control bg-secondary text-white border-0" id="porcentaje_descuento" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-oferta">Guardar</button>
                </div>
            </div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
});