/**
 * LÓGICA GLOBAL DEL PANEL ADMIN
 */

// Función principal de navegación
function cambiarSeccion(seccion, event) {
    if (event) event.preventDefault();

    // Gestión de clases active
    document.querySelectorAll('.nav-link-custom').forEach(link => link.classList.remove('active'));
    if (event) event.currentTarget.classList.add('active');

    const contenedor = document.getElementById('contenedor-principal');
    contenedor.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';

    switch (seccion) {
        case 'usuario':
            cargarSeccionUsuarios();
            break;
        case 'producto':
            cargarSeccionProductos(); // Carga todos los productos
            break;
        case 'pedido':
            cargarSeccionPedidos(); // La función que creamos antes
            break;
        case 'log':
            cargarSeccionLogs();
            break;
        case 'oferta':
            // Como ahora es una columna, podemos llamar a la misma función 
            // de productos pero pasarle un filtro, o simplemente avisar:
            cargarSeccionOfertas(); 
            break;
        default:
            contenedor.innerHTML = `<h2 class="text-white">Panel Mochistar</h2><p>Selecciona una opción.</p>`;
    }
}

// Función para mostrar secciones que aún no has programado
function mostrarAvisoDesarrollo(nombre) {
    const contenedor = document.getElementById('contenedor-principal');
    contenedor.innerHTML = `
        <div class="alert alert-secondary d-flex align-items-center bg-dark text-white border-secondary mt-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <div>La sección de <strong>${nombre}</strong> está actualmente en desarrollo.</div>
        </div>`;
}