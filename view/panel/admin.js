// Variable para el modal de Bootstrap
let modalProducto;

/**
 * 1. Gestión de Navegación Lateral
 */
function cambiarSeccion(seccion, event) {
    if (event) event.preventDefault();

    document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
    if (event) event.currentTarget.classList.add('active');

    switch (seccion) {
        case 'producto':
            cargarSeccionProductos();
            break;
        default:
            document.getElementById('contenedor-principal').innerHTML = `
                <h2 class="text-white">Sección ${seccion}</h2>
                <p class="text-muted">Contenido en desarrollo...</p>`;
    }
}

/**
 * 2. Cargar Tabla de Productos (GET)
 */
async function cargarSeccionProductos() {
    const contenedor = document.getElementById('contenedor-principal');
    contenedor.innerHTML = '<div class="text-white">Cargando productos...</div>';

    try {
        // CORREGIDO: Usamos el controlador 'api'
        const response = await fetch('index.php?controller=api&action=productos');
        const res = await response.json();

        if (res.estado === 'Exito') {
            contenedor.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white">Gestión de Productos</h2>
                    <button class="btn btn-primary" onclick="abrirModalProducto()">
                        <i class="bi bi-plus-lg"></i> Nuevo Producto
                    </button>
                </div>
                <div class="table-responsive bg-dark p-3 rounded shadow">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${res.data.map(p => `
                                <tr>
                                    <td>#${p.id_producto}</td>
                                    <td>${p.nombre}</td>
                                    <td>${p.precio_unidad}€</td>
                                    <td>${p.cantidad} uds</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info me-2" onclick="editarProducto(${p.id_producto})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(${p.id_producto})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>`;
        }
    } catch (error) {
        console.error("Error en el JS:", error);
        contenedor.innerHTML = `<div class="alert alert-danger">Error al cargar datos. Verifica la consola.</div>`;
    }
}

/**
 * 3. Lógica del Modal
 */
function abrirModalProducto(datos = null) {
    if (!modalProducto) {
        modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
    }

    const form = document.getElementById('formProducto');
    form.reset();
    document.getElementById('id_producto').value = "";

    if (datos) {
        document.getElementById('modalTitulo').innerText = "Editar Producto";
        document.getElementById('id_producto').value = datos.id_producto;
        document.getElementById('nombre').value = datos.nombre;
        document.getElementById('descripcion').value = datos.descripcion;
        document.getElementById('precio_unidad').value = datos.precio_unidad;
        document.getElementById('cantidad').value = datos.cantidad;
        document.getElementById('imagen').value = datos.imagen;
    } else {
        document.getElementById('modalTitulo').innerText = "Nuevo Producto";
    }

    modalProducto.show();
}

/**
 * 4. Guardar datos (POST para crear, PUT para editar)
 */
async function guardarProducto() {
    const id = document.getElementById('id_producto').value;
    const datos = {
        nombre: document.getElementById('nombre').value,
        descripcion: document.getElementById('descripcion').value,
        precio_unidad: parseFloat(document.getElementById('precio_unidad').value),
        cantidad: parseInt(document.getElementById('cantidad').value),
        imagen: document.getElementById('imagen').value || 'default.png'
    };

    if (id) datos.id_producto = id;

    try {
        // CORREGIDO: URL unificada controller=api
        const response = await fetch('index.php?controller=api&action=productos', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const res = await response.json();
        if (res.estado === 'Exito') {
            modalProducto.hide();
            cargarSeccionProductos();
        } else {
            alert("Error: " + res.mensaje);
        }
    } catch (error) {
        alert("Error de conexión con la API");
    }
}

/**
 * 5. Obtener datos para editar (GET individual)
 */
async function editarProducto(id) {
    try {
        // CORREGIDO: URL unificada controller=api
        const response = await fetch(`index.php?controller=api&action=productos&id=${id}`);
        const res = await response.json();
        if (res.estado === 'Exito') {
            abrirModalProducto(res.data);
        }
    } catch (error) {
        console.error("Error al obtener datos del producto");
    }
}

/**
 * 6. Eliminar (DELETE)
 */
async function confirmarEliminar(id) {
    if (!confirm("¿Seguro que quieres eliminar este producto?")) return;

    try {
        // CORREGIDO: URL unificada controller=api
        const response = await fetch('index.php?controller=api&action=productos', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        const res = await response.json();
        if (res.estado === 'Exito') {
            cargarSeccionProductos();
        } else {
            alert(res.mensaje);
        }
    } catch (error) {
        alert("Error al eliminar");
    }
}