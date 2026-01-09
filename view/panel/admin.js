//Esta es la función principal que decide qué "página" se muestra dentro del panel
function cambiarSeccion(seccion, event) {
    //Si venimos de un clic en un enlace <a>, evitamos que la página se refresque
    if (event) event.preventDefault();

    //Gestión visual: quitamos la marca de "activo" a todos los botones del menú...
    document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
    //...y se la ponemos solo al botón en el que acabamos de hacer clic
    if (event) event.currentTarget.classList.add('active');

    //Buscamos el hueco donde vamos a pintar el contenido y ponemos un círculo de carga (spinner)
    const contenedor = document.getElementById('contenedor-principal');
    contenedor.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';

    //Según la sección elegida, llamamos a una función u otra de nuestros archivos JS
    switch (seccion) {
        case 'usuario':
            cargarSeccionUsuarios(); //Llama a la lógica de la tabla de usuarios
            break;
        case 'producto':
            cargarSeccionProductos(); //Llama a la lógica del inventario de mochis
            break;
        case 'pedido':
            cargarSeccionPedidos(); //Llama a la gestión de tickets de compra
            break;
        case 'log':
            cargarSeccionLogs(); //Muestra el historial de quién ha hecho qué (Auditoría)
            break;
        case 'oferta':
            //Ideal para mochis de temporada o con descuento
            cargarSeccionOfertas(); 
            break;
        default:
            //Si no hay sección elegida o algo falla, mostramos el mensaje de bienvenida
            contenedor.innerHTML = `<h2 class="text-white">Panel Mochistar</h2><p>Selecciona una opción en el menú de la izquierda.</p>`;
    }
}

//Si no puede poner nada
function mostrarAvisoDesarrollo(nombre) {
    const contenedor = document.getElementById('contenedor-principal');
    contenedor.innerHTML = `
        <div class="alert alert-secondary d-flex align-items-center bg-dark text-white border-secondary mt-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <div>La sección de <strong>${nombre}</strong> está actualmente en desarrollo. ¡Estamos trabajando en ello!</div>
        </div>`;
}