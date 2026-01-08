/**
 * GESTIÓN DE USUARIOS - CORREGIDO
 */
let instanceModalUsuario = null;

async function cargarSeccionUsuarios() {
    const contenedor = document.getElementById('contenedor-principal');
    
    contenedor.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white">Gestión de Usuarios</h2>
            <button class="btn btn-primary" onclick="abrirModalUsuario()">
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
        // Importante: La URL debe coincidir con tu ruteo
        const response = await fetch('index.php?controller=api&action=usuarios');
        const result = await response.json();
        const tbody = document.getElementById('tabla-usuarios-body');
        
        console.log("Datos recibidos:", result); // Para depurar en F12

        if (result.estado === 'Exito' && result.data) {
            tbody.innerHTML = result.data.map(u => `
                <tr>
                    <td>#${u.id_usuario}</td> 
                    <td>${u.nombre} ${u.apellido || ''}</td>
                    <td>${u.email}</td>
                    <td>
                        <span class="badge ${u.rol === 'admin' ? 'bg-danger' : 'bg-success'}">
                            ${u.rol.toUpperCase()}
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-warning me-2" onclick="editarUsuario(${u.id_usuario})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${u.id_usuario})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-muted">No se encontraron usuarios.</td></tr>';
        }
    } catch (error) {
        console.error("Error en el fetch:", error);
        document.getElementById('tabla-usuarios-body').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al conectar con la API</td></tr>';
    }
}

// ... (Resto de funciones abrirModal, guardar, eliminar se mantienen igual 
// pero asegurando que usen 'id_usuario')

// 2. Abrir Modal (Crear o Editar)
function abrirModalUsuario(datos = null) {
    if (!instanceModalUsuario) {
        instanceModalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
    }

    const form = document.getElementById('formUsuario');
    form.reset();
    
    if (datos) {
        // MODO EDICIÓN: Usamos id_usuario (así viene del DAO)
        document.getElementById('id_usuario_form').value = datos.id_usuario; 
        document.getElementById('u_nombre').value = datos.nombre;
        document.getElementById('u_apellido').value = datos.apellido || '';
        document.getElementById('u_email').value = datos.email;
        document.getElementById('u_rol').value = datos.rol;
        document.getElementById('modalTituloUsuario').innerText = 'Editar Usuario #' + datos.id_usuario;
        document.getElementById('div-contrasena').style.display = 'none';
    } else {
        // MODO NUEVO
        document.getElementById('id_usuario_form').value = '';
        document.getElementById('modalTituloUsuario').innerText = 'Nuevo Usuario';
        document.getElementById('div-contrasena').style.display = 'block';
    }
    instanceModalUsuario.show();
}

// 3. Obtener datos para editar
async function editarUsuario(id) {
    try {
        const response = await fetch(`index.php?controller=api&action=usuarios&id=${id}`);
        const result = await response.json();
        if (result.estado === 'Exito') {
            abrirModalUsuario(result.data);
        }
    } catch (error) {
        alert("Error al obtener datos");
    }
}

// 4. Guardar (POST o PUT)
async function guardarUsuario() {
    const id = document.getElementById('id_usuario_form').value;
    const esEdicion = id !== "";
    
    const datos = {
        id_usuario: id, // Enviamos id_usuario para que el PHP lo reciba bien
        nombre: document.getElementById('u_nombre').value,
        apellido: document.getElementById('u_apellido').value,
        email: document.getElementById('u_email').value,
        rol: document.getElementById('u_rol').value
    };

    if (!esEdicion) {
        datos.contrasena = document.getElementById('u_contrasena').value;
    }

    try {
        const response = await fetch('index.php?controller=api&action=usuarios', {
            method: esEdicion ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const result = await response.json();
        if (result.estado === 'Exito') {
            instanceModalUsuario.hide();
            cargarSeccionUsuarios();
        } else {
            alert(result.mensaje);
        }
    } catch (error) {
        alert("Error de conexión con el servidor");
    }
}

// 5. Eliminar
async function eliminarUsuario(id) {
    // 1. Verificación de seguridad
    // Si no tienes definida ADMIN_ACTUAL_ID, comenta la siguiente línea para probar
    // if (typeof ADMIN_ACTUAL_ID !== 'undefined' && id == ADMIN_ACTUAL_ID) return alert("No puedes eliminarte a ti mismo.");
    
    if (!confirm(`¿Estás seguro de eliminar al usuario #${id}?`)) return;

    console.log("Intentando eliminar ID:", id); // Verifica esto en F12

    try {
        const response = await fetch('index.php?controller=api&action=usuarios', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id }) // Enviamos { "id": X }
        });

        const result = await response.json();
        
        if (result.estado === 'Exito') {
            alert("Usuario eliminado correctamente");
            cargarSeccionUsuarios(); // Recargamos la tabla
        } else {
            alert("Error: " + result.mensaje);
        }
    } catch (error) {
        console.error("Error en la petición DELETE:", error);
        alert("Error de conexión al eliminar");
    }
}