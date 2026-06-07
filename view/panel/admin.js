/**
 * Clase Base abstracta para los gestores de secciones (POO - Clases JS)
 */
class BaseManager {
    constructor(contenedorId) {
        this.contenedor = document.getElementById(contenedorId);
        if (!this.contenedor) {
            throw new Error(`Contenedor con id "${contenedorId}" no encontrado.`);
        }
    }

    /**
     * Método abstracto que debe implementar cada sección
     */
    async cargar() {
        throw new Error("El método cargar() debe ser implementado.");
    }

    /**
     * Muestra un spinner de carga en el panel principal
     */
    mostrarCargando(mensaje = "Cargando datos...") {
        this.contenedor.innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2">${mensaje}</p>
            </div>`;
    }

    /**
     * Muestra un banner de error si falla la comunicación con la API
     */
    mostrarError(mensaje = "Error al conectar con el servidor.") {
        this.contenedor.innerHTML = `
            <div class="alert alert-danger border-0 bg-dark text-white border-danger mt-4">
                <i class="bi bi-x-circle me-2"></i>
                <strong>Error:</strong> ${mensaje}
            </div>`;
    }
}

/**
 * Controlador principal del panel de administración
 */
class AdminController {
    constructor() {
        this.managers = {};
        this.currentSection = null;
    }

    /**
     * Inicializa los gestores y los manejadores de eventos
     */
    init() {
        // Inicializar los manejadores de cada sección
        this.managers = {
            producto: new ProductoManager('contenedor-principal'),
            usuario: new UsuarioManager('contenedor-principal'),
            log: new LogManager('contenedor-principal'),
            pedido: new PedidoManager('contenedor-principal'),
            oferta: new OfertaManager('contenedor-principal')
        };

        this.setupEventListeners();
        
        // Cargar sección de ofertas por defecto al entrar
        this.cambiarSeccion('oferta');
    }

    /**
     * Asocia los eventos de la barra lateral (onclick NO, listeners SI)
     */
    setupEventListeners() {
        const links = document.querySelectorAll('.list-group .nav-link-custom');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Quitar clase active a todos los elementos del menú
                links.forEach(l => l.classList.remove('active'));
                
                // Añadir clase active al enlace actual
                link.classList.add('active');
                
                // Obtener sección desde el atributo data-section (DOM y JS Avanzado)
                const seccion = link.getAttribute('data-section');
                this.cambiarSeccion(seccion);
            });
        });
    }

    /**
     * Alterna la vista cargando dinámicamente la sección seleccionada (Async/Await)
     */
    async cambiarSeccion(seccion) {
        if (!this.managers[seccion]) {
            const contenedor = document.getElementById('contenedor-principal');
            contenedor.innerHTML = `
                <div class="alert alert-secondary d-flex align-items-center bg-dark text-white border-secondary mt-4" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div>La sección de <strong>${seccion}</strong> no está disponible.</div>
                </div>`;
            return;
        }

        this.currentSection = seccion;
        try {
            await this.managers[seccion].cargar();
        } catch (error) {
            console.error(`Error al cargar la sección ${seccion}:`, error);
        }
    }
}

// Iniciar controlador principal cuando el DOM esté listo
let adminController;
document.addEventListener('DOMContentLoaded', () => {
    adminController = new AdminController();
    adminController.init();
});