/**
 * Carga la tabla de usuarios en el contenedor principal
 */
// Al principio de view/panel/usuario.js
alert("Archivo usuario.js cargado correctamente");
async function cargarSeccionUsuarios() {
    const contenedor = document.getElementById('contenedor-principal');
    
    // 1. Dibujamos la estructura básica (Título, botón y cabecera de tabla)
    contenedor.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white">Gestión de Usuarios</h2>
            <button class="btn btn-primary" onclick="abrirModalUsuario()">
                <i class="bi bi-person-plus me-2"></i> Nuevo Usuario
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover border-secondary">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios-body">
                    <tr><td colspan="5" class="text-center text-muted p-4">Cargando usuarios...</td></tr>
                </tbody>
            </table>
        </div>`;

    // 2. Pedimos los datos al servidor
    try {
        const response = await fetch('apiController.php?action=get_usuarios');
        const usuarios = await response.json();
        const tbody = document.getElementById('tabla-usuarios-body');
        
        tbody.innerHTML = ''; // Limpiamos el "Cargando..."

        if (usuarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay usuarios registrados.</td></tr>';
            return;
        }

        // 3. Pintamos cada fila
        usuarios.forEach(u => {
            const badgeClass = u.rol === 'admin' ? 'bg-danger' : 'bg-success';
            tbody.innerHTML += `
                <tr>
                    <td>${u.id}</td>
                    <td>${u.nombre} ${u.apellido}</td>
                    <td>${u.email}</td>
                    <td><span class="badge ${badgeClass}">${u.rol}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-warning me-2" onclick="editarUsuario(${u.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${u.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });
    } catch (error) {
        console.error("Error:", error);
        document.getElementById('tabla-usuarios-body').innerHTML = 
            '<tr><td colspan="5" class="text-center text-danger">Error al cargar datos. Revisa la consola (F12).</td></tr>';
    }
}

/**
 * Funciones para el Modal (Abrir, Guardar, etc.)
 */
function abrirModalUsuario() {
    document.getElementById('formUsuario').reset();
    document.getElementById('id_usuario_form').value = '';
    document.getElementById('modalTituloUsuario').innerText = 'Nuevo Usuario';
    document.getElementById('div-contrasena').style.display = 'block';
    
    const myModal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    myModal.show();
}

async function guardarUsuario() {
    const datos = {
        id: document.getElementById('id_usuario_form').value,
        nombre: document.getElementById('u_nombre').value,
        apellido: document.getElementById('u_apellido').value,
        email: document.getElementById('u_email').value,
        telefono: document.getElementById('u_telefono').value,
        rol: document.getElementById('u_rol').value,
        contrasena: document.getElementById('u_contrasena').value
    };

    // Validación básica
    if(!datos.nombre || !datos.email) return alert("Nombre y Email son obligatorios");

    try {
        const action = datos.id ? 'update_usuario' : 'add_usuario';
        const response = await fetch(`apiController.php?action=${action}`, {
            method: 'POST',
            body: JSON.stringify(datos)
        });
        
        const result = await response.json();
        if(result.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
            cargarSeccionUsuarios(); // Recargamos la tabla
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        console.error("Error al guardar:", error);
    }
}