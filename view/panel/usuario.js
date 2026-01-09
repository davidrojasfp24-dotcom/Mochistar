//Variable para controlar el "pop-up" (modal) de Bootstrap y poder abrirlo o cerrarlo
let instanceModalUsuario = null;

//Función principal para pintar la tabla de usuarios en la pantalla
async function cargarSeccionUsuarios() {
    const contenedor = document.getElementById('contenedor-principal');
    
    //Dibujamos la estructura básica: el título, el botón de "Nuevo" y la cabecera de la tabla
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
        //Pedimos los usuarios a nuestra API (el apiController de PHP)
        const response = await fetch('index.php?controller=api&action=usuarios');
        const result = await response.json();
        const tbody = document.getElementById('tabla-usuarios-body');
        
        console.log("Datos recibidos:", result); // Esto nos ayuda a ver en la consola (F12) si todo llega bien

        //Si la API responde con éxito, recorremos los usuarios y los metemos en la tabla
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

//Función para mostrar el formulario (ya sea para crear uno nuevo o editar uno existente)
function abrirModalUsuario(datos = null) {
    //Si el modal no existe todavía, lo inicializamos con Bootstrap
    if (!instanceModalUsuario) {
        instanceModalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
    }

    const form = document.getElementById('formUsuario');
    form.reset(); //Limpiamos el formulario por si acaso
    
    if (datos) {
        // --- MODO EDICIÓN ---
        document.getElementById('id_usuario_form').value = datos.id_usuario; 
        document.getElementById('u_nombre').value = datos.nombre;
        document.getElementById('u_apellido').value = datos.apellido || '';
        document.getElementById('u_email').value = datos.email;
        document.getElementById('u_rol').value = datos.rol;
        document.getElementById('modalTituloUsuario').innerText = 'Editar Usuario #' + datos.id_usuario;
        
        //Al editar no dejamos cambiar la contraseña por aquí (por seguridad)
        document.getElementById('div-contrasena').style.display = 'none';
    } else {
        document.getElementById('id_usuario_form').value = '';
        document.getElementById('modalTituloUsuario').innerText = 'Nuevo Usuario';
        //Para usuarios nuevos sí mostramos el campo de contraseña
        document.getElementById('div-contrasena').style.display = 'block';
    }
    instanceModalUsuario.show(); //Mostramos el pop-up
}

//Función que busca los datos de un usuario concreto para poder editarlos
async function editarUsuario(id) {
    try {
        const response = await fetch(`index.php?controller=api&action=usuarios&id=${id}`);
        const result = await response.json();
        if (result.estado === 'Exito') {
            //Cuando los datos llegan, abrimos el modal en modo edición
            abrirModalUsuario(result.data);
        }
    } catch (error) {
        alert("Error al obtener datos");
    }
}

//Función que envía los datos al servidor (crear o actualizar)
async function guardarUsuario() {
    const id = document.getElementById('id_usuario_form').value;
    const esEdicion = id !== ""; //Si hay ID, es que estamos editando
    
    //Recogemos todos los datos del formulario
    const datos = {
        id_usuario: id, 
        nombre: document.getElementById('u_nombre').value,
        apellido: document.getElementById('u_apellido').value,
        email: document.getElementById('u_email').value,
        rol: document.getElementById('u_rol').value
    };

    //Solo pedimos la contraseña si el usuario es nuevo
    if (!esEdicion) {
        datos.contrasena = document.getElementById('u_contrasena').value;
    }

    try {
        //Si editamos usamos PUT, si es nuevo usamos POST
        const response = await fetch('index.php?controller=api&action=usuarios', {
            method: esEdicion ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const result = await response.json();
        if (result.estado === 'Exito') {
            instanceModalUsuario.hide(); //Cerramos el modal
            cargarSeccionUsuarios();     //Recargamos la tabla para ver los cambios
        } else {
            alert(result.mensaje); //Si el email ya existe o hay error, avisamos
        }
    } catch (error) {
        alert("Error de conexión con el servidor");
    }
}

//Función para borrar usuarios definitivamente
async function eliminarUsuario(id) {
    //Pedimos confirmación para que nadie borre a un usuario por error
    if (!confirm(`¿Estás seguro de eliminar al usuario #${id}?`)) return;

    try {
        const response = await fetch('index.php?controller=api&action=usuarios', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id }) //Mandamos el ID al servidor
        });

        const result = await response.json();
        
        if (result.estado === 'Exito') {
            alert("Usuario eliminado correctamente");
            cargarSeccionUsuarios(); //Actualizamos la lista automáticamente
        } else {
            alert("Error: " + result.mensaje);
        }
    } catch (error) {
        console.error("Error en la petición DELETE:", error);
        alert("Error de conexión al eliminar");
    }
}