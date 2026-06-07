/**
 * Gestor de la sección de Productos (hereda de BaseManager)
 */
class ProductoManager extends BaseManager {
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
        const btnGuardar = document.getElementById('btn-guardar-producto');
        if (btnGuardar) {
            btnGuardar.addEventListener('click', () => this.guardar());
        }

        // Delegación de eventos en el contenedor principal (JS Avanzado)
        this.contenedor.addEventListener('click', (event) => {
            if (adminController.currentSection !== 'producto') return;

            const btnNuevo = event.target.closest('[data-action="nuevo-producto"]');
            if (btnNuevo) {
                this.abrirModal();
                return;
            }

            const btnEditar = event.target.closest('[data-action="editar-producto"]');
            if (btnEditar) {
                const id = btnEditar.getAttribute('data-id');
                this.editar(id);
                return;
            }

            const btnEliminar = event.target.closest('[data-action="eliminar-producto"]');
            if (btnEliminar) {
                const id = btnEliminar.getAttribute('data-id');
                this.confirmarEliminar(id);
                return;
            }
        });
    }

    /**
     * Carga todos los productos desde la base de datos (Async/Await)
     */
    async cargar() {
        this.mostrarCargando("Cargando catálogo de productos...");

        this.contenedor.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white">Gestión de Productos</h2>
                <button class="btn btn-primary" data-action="nuevo-producto">
                    <i class="bi bi-plus-lg me-2"></i> Nuevo Producto
                </button>
            </div>
            <div class="table-responsive bg-dark p-3 rounded shadow">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>NOMBRE</th>
                            <th>PRECIO</th>
                            <th>STOCK</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-productos-body">
                        <tr><td colspan="5" class="text-center p-4">Cargando productos...</td></tr>
                    </tbody>
                </table>
            </div>`;

        try {
            const response = await fetch('index.php?controller=api&action=productos');
            const res = await response.json();
            const tbody = document.getElementById('tabla-productos-body');

            if (res.estado === 'Exito' && res.data.length > 0) {
                // Desestructuración de propiedades dentro del map (JS Avanzado)
                tbody.innerHTML = res.data.map(({ id_producto, nombre, precio_unidad, cantidad }) => `
                    <tr>
                        <td class="text-muted">#${id_producto}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold">${nombre}</span>
                            </div>
                        </td>
                        <td><span class="text-info">${precio_unidad}€</span></td>
                        <td>
                            <span class="badge ${cantidad < 10 ? 'bg-warning text-dark' : 'bg-secondary'}">
                                ${cantidad} uds
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-info me-2" data-action="editar-producto" data-id="${id_producto}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-action="eliminar-producto" data-id="${id_producto}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-muted">No hay productos en la base de datos.</td></tr>';
            }
        } catch (error) {
            console.error("Error al cargar productos:", error);
            this.mostrarError("Error al conectar con la API de productos.");
        }
    }

    /**
     * Abre el modal para añadir o editar un producto
     */
    abrirModal(datos = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalProducto'));
        }
        if (!this.form) {
            this.form = document.getElementById('formProducto');
        }
        
        this.form.reset();
        document.getElementById('id_producto').value = "";

        if (datos) {
            // Desestructuración de datos cargados del API (JS Avanzado)
            const { id_producto, nombre, descripcion, precio_unidad, cantidad, imagen } = datos;
            document.getElementById('modalTitulo').innerText = "Editar Producto #" + id_producto;
            document.getElementById('id_producto').value = id_producto;
            document.getElementById('nombre').value = nombre;
            document.getElementById('descripcion').value = descripcion || '';
            document.getElementById('precio_unidad').value = precio_unidad;
            document.getElementById('cantidad').value = cantidad;
            document.getElementById('imagen').value = imagen || '';
        } else {
            document.getElementById('modalTitulo').innerText = "Nuevo Producto";
        }

        this.modal.show();
    }

    /**
     * Solicita datos de un producto específico para su edición (Promises)
     */
    async editar(id) {
        try {
            const response = await fetch(`index.php?controller=api&action=productos&id=${id}`);
            const res = await response.json();
            if (res.estado === 'Exito') {
                this.abrirModal(res.data);
            } else {
                alert("No se pudo obtener la información del producto.");
            }
        } catch (error) {
            console.error("Error al editar:", error);
        }
    }

    /**
     * Guarda el producto (crear o actualizar) enviando datos en JSON (Spread Operator)
     */
    async guardar() {
        const id = document.getElementById('id_producto').value;

        const datos = {
            nombre: document.getElementById('nombre').value.trim(),
            descripcion: document.getElementById('descripcion').value.trim(),
            precio_unidad: parseFloat(document.getElementById('precio_unidad').value),
            cantidad: parseInt(document.getElementById('cantidad').value),
            imagen: document.getElementById('imagen').value.trim() || 'default.png'
        };

        if (!datos.nombre || isNaN(datos.precio_unidad)) {
            return alert("Por favor, rellena los campos obligatorios.");
        }

        // Operador Spread para concatenar propiedades del payload (JS Avanzado)
        const payload = id ? { ...datos, id_producto: id } : { ...datos };

        try {
            const response = await fetch('index.php?controller=api&action=productos', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const res = await response.json();

            if (res.estado === 'Exito') {
                this.modal.hide();
                this.cargar();
            } else {
                alert("Error del servidor: " + res.mensaje);
            }
        } catch (error) {
            alert("Error crítico de comunicación con la API");
        }
    }

    /**
     * Elimina un producto por su ID
     */
    async confirmarEliminar(id) {
        if (!confirm("¿Estás seguro de que deseas eliminar este mochi? Esta acción no se puede deshacer.")) return;

        try {
            const response = await fetch('index.php?controller=api&action=productos', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });

            const res = await response.json();

            if (res.estado === 'Exito') {
                this.cargar();
            } else {
                alert("No se pudo eliminar: " + res.mensaje);
            }
        } catch (error) {
            alert("Error al intentar conectar con el servidor para eliminar.");
        }
    }
}