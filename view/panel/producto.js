/**
 * GESTIÓN DE PRODUCTOS (MOCHIS)
 */
let instanceModalProducto = null;

/**
 * 1. Cargar la tabla de productos
 * Se ejecuta cuando pulsas "Productos" en el sidebar
 */
async function cargarSeccionProductos() {
    const contenedor = document.getElementById('contenedor-principal');
    
    // Dibujamos la estructura de la tabla
    contenedor.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white">Gestión de Productos</h2>
            <button class="btn btn-primary" onclick="abrirModalProducto()">
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
            tbody.innerHTML = res.data.map(p => `
                <tr>
                    <td class="text-muted">#${p.id_producto}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold">${p.nombre}</span>
                        </div>
                    </td>
                    <td><span class="text-info">${p.precio_unidad}€</span></td>
                    <td>
                        <span class="badge ${p.cantidad < 10 ? 'bg-warning text-dark' : 'bg-secondary'}">
                            ${p.cantidad} uds
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-info me-2" onclick="editarProducto(${p.id_producto})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(${p.id_producto})">
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
        document.getElementById('contenedor-principal').innerHTML = `<div class="alert alert-danger">Error de conexión con el servidor.</div>`;
    }
}

/**
 * 2. Lógica del Modal (Abrir para crear o editar)
 */
function abrirModalProducto(datos = null) {
    if (!instanceModalProducto) {
        instanceModalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
    }

    const form = document.getElementById('formProducto');
    form.reset();
    
    // Limpiamos el ID oculto
    document.getElementById('id_producto').value = "";

    if (datos) {
        // Si vienen datos, es MODO EDICIÓN
        document.getElementById('modalTitulo').innerText = "Editar Producto #" + datos.id_producto;
        document.getElementById('id_producto').value = datos.id_producto;
        document.getElementById('nombre').value = datos.nombre;
        document.getElementById('descripcion').value = datos.descripcion;
        document.getElementById('precio_unidad').value = datos.precio_unidad;
        document.getElementById('cantidad').value = datos.cantidad;
        document.getElementById('imagen').value = datos.imagen;
    } else {
        // Si no vienen datos, es MODO NUEVO
        document.getElementById('modalTitulo').innerText = "Nuevo Producto";
    }

    instanceModalProducto.show();
}

/**
 * 3. Obtener datos de un producto específico para editar
 */
async function editarProducto(id) {
    try {
        const response = await fetch(`index.php?controller=api&action=productos&id=${id}`);
        const res = await response.json();
        if (res.estado === 'Exito') {
            abrirModalProducto(res.data);
        } else {
            alert("No se pudo obtener la información del producto.");
        }
    } catch (error) {
        console.error("Error al editar:", error);
    }
}

/**
 * 4. Guardar datos (POST para crear, PUT para actualizar)
 */
async function guardarProducto() {
    const id = document.getElementById('id_producto').value;
    
    // Recolectamos los datos del formulario
    const datos = {
        nombre: document.getElementById('nombre').value,
        descripcion: document.getElementById('descripcion').value,
        precio_unidad: parseFloat(document.getElementById('precio_unidad').value),
        cantidad: parseInt(document.getElementById('cantidad').value),
        imagen: document.getElementById('imagen').value || 'default.png'
    };

    // Validaciones básicas
    if (!datos.nombre || isNaN(datos.precio_unidad)) {
        return alert("Por favor, rellena los campos obligatorios.");
    }

    // Si hay ID, lo añadimos al objeto para que el backend sepa cuál editar
    if (id) datos.id_producto = id;

    try {
        const response = await fetch('index.php?controller=api&action=productos', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const res = await response.json();
        
        if (res.estado === 'Exito') {
            instanceModalProducto.hide();
            cargarSeccionProductos(); // Recargamos la tabla para ver los cambios
        } else {
            alert("Error del servidor: " + res.mensaje);
        }
    } catch (error) {
        alert("Error crítico de comunicación con la API");
    }
}

/**
 * 5. Eliminar un producto (DELETE)
 */
async function confirmarEliminar(id) {
    if (!confirm("¿Estás seguro de que deseas eliminar este mochi? Esta acción no se puede deshacer.")) return;

    try {
        const response = await fetch('index.php?controller=api&action=productos', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        
        const res = await response.json();
        
        if (res.estado === 'Exito') {
            cargarSeccionProductos();
        } else {
            alert("No se pudo eliminar: " + res.mensaje);
        }
    } catch (error) {
        alert("Error al intentar conectar con el servidor para eliminar.");
    }
}