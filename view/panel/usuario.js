/**
 * Gestor de la sección de Usuarios (hereda de BaseManager)
 */
class UsuarioManager extends BaseManager {
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
        // Guardar cambios desde el botón del modal (onclick NO, listeners SI)
        const btnGuardar = document.getElementById('btn-guardar-usuario');
        if (btnGuardar) {
            btnGuardar.addEventListener('click', () => this.guardar());
        }

        // Delegación de eventos en el contenedor principal (JS Avanzado)
        this.contenedor.addEventListener('click', (event) => {
            if (adminController.currentSection !== 'usuario') return;

            const btnNuevo = event.target.closest('[data-action="nuevo-usuario"]');
            if (btnNuevo) {
                this.abrirModal();
                return;
            }

            const btnEditar = event.target.closest('[data-action="editar-usuario"]');
            if (btnEditar) {
                const id = btnEditar.getAttribute('data-id');
                this.editar(id);
                return;
            }

            const btnEliminar = event.target.closest('[data-action="eliminar-usuario"]');
            if (btnEliminar) {
                const id = btnEliminar.getAttribute('data-id');
                this.eliminar(id);
                return;
            }
        });
    }

    /**
     * Carga todos los usuarios desde la base de datos (Async/Await)
     */
    async cargar() {
        this.mostrarCargando("Cargando usuarios registrados...");

        this.contenedor.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white">Gestión de Usuarios</h2>
                <button class="btn btn-primary" data-action="nuevo-usuario">
                    <i class="bi bi-person-plus me-2"></i> Nuevo Usuario
                </button>
            </div>
            <div class="table-responsive bg-dark p-3 rounded shadow">
                <table class="table table-dark table-hover align-middle border-secondary">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOMBRE COMPLETO</th>
                            <th>EMAIL</th>
                            <th>ROL</th>
                            <th class="text-end">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios-body">
                        <tr><td colspan="5" class="text-center p-4">Cargando datos...</td></tr>
                    </tbody>
                </table>
            </div>`;

        try {
            const response = await fetch('index.php?controller=api&action=usuarios');
            const result = await response.json();
            const tbody = document.getElementById('tabla-usuarios-body');

            if (result.estado === 'Exito' && result.data) {
                // Desestructuración de propiedades en la plantilla (JS Avanzado)
                tbody.innerHTML = result.data.map(({ id_usuario, nombre, apellido, email, rol }) => `
                    <tr>
                        <td>#${id_usuario}</td> 
                        <td>${nombre} ${apellido || ''}</td>
                        <td>${email}</td>
                        <td>
                            <span class="badge ${rol === 'admin' ? 'bg-danger' : 'bg-success'}">
                                ${rol.toUpperCase()}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning me-2" data-action="editar-usuario" data-id="${id_usuario}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-action="eliminar-usuario" data-id="${id_usuario}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-muted">No se encontraron usuarios.</td></tr>';
            }
        } catch (error) {
            console.error("Error en el fetch de usuarios:", error);
            this.mostrarError("Error al conectar con la API de usuarios.");
        }
    }

    /**
     * Abre el modal para crear o editar un usuario
     */
    abrirModal(datos = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        }
        if (!this.form) {
            this.form = document.getElementById('formUsuario');
        }
        
        this.form.reset();
        document.getElementById('id_usuario_form').value = '';

        if (datos) {
            // Desestructuración del objeto datos (JS Avanzado)
            const { id_usuario, nombre, apellido, email, rol } = datos;
            document.getElementById('id_usuario_form').value = id_usuario; 
            document.getElementById('u_nombre').value = nombre;
            document.getElementById('u_apellido').value = apellido || '';
            document.getElementById('u_email').value = email;
            document.getElementById('u_rol').value = rol;
            document.getElementById('modalTituloUsuario').innerText = 'Editar Usuario #' + id_usuario;
            document.getElementById('div-contrasena').style.display = 'none';
        } else {
            document.getElementById('modalTituloUsuario').innerText = 'Nuevo Usuario';
            document.getElementById('div-contrasena').style.display = 'block';
        }
        this.modal.show();
    }

    /**
     * Obtiene los datos de un usuario para editarlo
     */
    async editar(id) {
        try {
            const response = await fetch(`index.php?controller=api&action=usuarios&id=${id}`);
            const result = await response.json();
            if (result.estado === 'Exito') {
                this.abrirModal(result.data);
            }
        } catch (error) {
            alert("Error al obtener datos");
        }
    }

    /**
     * Guarda el usuario (crear o actualizar) (Async/Await)
     */
    async guardar() {
        const id = document.getElementById('id_usuario_form').value;
        const esEdicion = id !== ""; 
        
        const datos = {
            id_usuario: id, 
            nombre: document.getElementById('u_nombre').value.trim(),
            apellido: document.getElementById('u_apellido').value.trim(),
            email: document.getElementById('u_email').value.trim(),
            rol: document.getElementById('u_rol').value
        };

        if (!esEdicion) {
            datos.password = document.getElementById('u_contrasena').value;
        }

        try {
            const response = await fetch('index.php?controller=api&action=usuarios', {
                method: esEdicion ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const result = await response.json();
            if (result.estado === 'Exito') {
                this.modal.hide();
                this.cargar();
            } else {
                alert(result.mensaje);
            }
        } catch (error) {
            alert("Error de conexión con el servidor");
        }
    }

    /**
     * Elimina un usuario por su ID
     */
    async eliminar(id) {
        if (!confirm(`¿Estás seguro de eliminar al usuario #${id}?`)) return;

        try {
            const response = await fetch('index.php?controller=api&action=usuarios', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });

            const result = await response.json();
            
            if (result.estado === 'Exito') {
                alert("Usuario eliminado correctamente");
                this.cargar();
            } else {
                alert("Error: " + result.mensaje);
            }
        } catch (error) {
            console.error("Error en la petición DELETE:", error);
            alert("Error de conexión al eliminar");
        }
    }
}