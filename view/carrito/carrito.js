/**
 * CARRITO DE COMPRAS - LÓGICA CENTRAL
 */

document.addEventListener('DOMContentLoaded', () => {
    actualizarInterfaz();
});

// Función maestra que refresca lo que sea necesario según la página
function actualizarInterfaz() {
    // 1. Siempre intenta actualizar los botones del catálogo si existen
    actualizarBotonesCatalogo();

    // 2. Si existe el contenedor de lista, estamos en la vista carrito
    if (document.getElementById('lista-productos')) {
        renderizarCarrito();
    }
}

// --- LÓGICA PARA EL CATÁLOGO (Botón que se transforma) ---

function actualizarBotonesCatalogo() {
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    // Buscamos todos los divs que reservamos en el PHP para los botones
    const contenedores = document.querySelectorAll('[id^="contenedor-boton-"]');
    
    contenedores.forEach(div => {
        const idProducto = parseInt(div.id.replace('contenedor-boton-', ''));
        const item = carrito.find(p => p.id === idProducto);
        
        if (item) {
            // Si el producto ya está en el carrito, mostramos el selector azul
            div.innerHTML = `
                <div class="selector-cantidad">
                    <button class="btn-flecha" onclick="cambiarCantidadGlobal(${idProducto}, -1)">-</button>
                    <span class="fw-bold">${item.cantidad}</span>
                    <button class="btn-flecha" onclick="cambiarCantidadGlobal(${idProducto}, 1)">+</button>
                </div>`;
        } else {
            // Si no está, mostramos el botón COMPRAR azul original
            div.innerHTML = `
                <button class="btn-finalizar-compra" style="padding: 10px; font-size: 1.1rem; margin:0;" 
                    onclick="agregarAlCarrito(${idProducto})">
                    COMPRAR
                </button>`;
        }
    });
}

function agregarAlCarrito(id) {
    const contenedor = document.getElementById(`contenedor-boton-${id}`);
    
    // Leemos los datos de los atributos 'data-' del HTML (puestos por PHP)
    const nuevoProducto = {
        id: id,
        nombre: contenedor.getAttribute('data-nombre'),
        precio: parseFloat(contenedor.getAttribute('data-precio')),
        image: contenedor.getAttribute('data-image'),
        cantidad: 1
    };

    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    carrito.push(nuevoProducto);
    localStorage.setItem('carrito', JSON.stringify(carrito));
    
    actualizarInterfaz();
}

// Función unificada para subir, bajar o borrar (funciona desde cualquier página)
function cambiarCantidadGlobal(id, cambio) {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const index = carrito.findIndex(p => p.id === id);

    if (index !== -1) {
        carrito[index].cantidad += cambio;
        
        // Si la cantidad llega a 0 (o menos), se elimina el producto
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
        }
        
        localStorage.setItem('carrito', JSON.stringify(carrito));
        actualizarInterfaz();
    }
}

// --- LÓGICA PARA LA VISTA DETALLADA DEL CARRITO ---

function renderizarCarrito() {
    const lista = document.getElementById('lista-productos');
    const resumen = document.getElementById('resumen-totales');
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];

    if (carrito.length === 0) {
        lista.innerHTML = '<h4 class="text-white opacity-50">No hay productos en tu carrito.</h4>';
        resumen.innerHTML = '<p class="text-white">Subtotal: 0,00€</p>';
        return;
    }

    let htmlContenido = '';
    let subtotal = 0;

    carrito.forEach(item => {
        const totalFila = item.precio * item.cantidad;
        subtotal += totalFila;

        htmlContenido += `
            <div class="cart-item d-flex align-items-center justify-content-between mb-4 text-white p-3" 
                 style="background: rgba(255,255,255,0.05); border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <img src="view/home/img/${item.image}" alt="${item.nombre}" 
                         style="width:70px; height:70px; border-radius:12px; margin-right:20px; object-fit:cover;">
                    <div>
                        <h5 class="mb-0">${item.nombre}</h5>
                        <small class="text-white-50">${item.precio.toFixed(2)}€ x unidad</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="qty-controls d-flex align-items-center me-4">
                        <button class="btn-qty" onclick="cambiarCantidadGlobal(${item.id}, -1)">-</button>
                        <span class="mx-3 fw-bold">${item.cantidad}</span>
                        <button class="btn-qty" onclick="cambiarCantidadGlobal(${item.id}, 1)">+</button>
                    </div>
                    
                    <button class="btn-remove" onclick="cambiarCantidadGlobal(${item.id}, -${item.cantidad})">
                         <i class="bi bi-trash"></i> Borrar
                    </button>
                </div>
            </div>`;
    });

    const impuesto = subtotal * 0.10;
    const totalFinal = subtotal + impuesto;

    lista.innerHTML = htmlContenido;
    resumen.innerHTML = `
        <div class="text-white">
            <p class="mb-1">Subtotal: ${subtotal.toFixed(2)}€</p>
            <p class="mb-1 text-white-50">IVA (10%): ${impuesto.toFixed(2)}€</p>
            <div class="total-text" style="font-size: 2.5rem; font-weight: bold; border-top: 1px solid #333; margin-top: 15px; padding-top: 10px;">
                Total: ${totalFinal.toFixed(2)}€
            </div>
        </div>`;
}

// --- ACCIÓN FINAL ---

function finalizarCompra() {
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    if (carrito.length === 0) {
        alert("Añade algún producto antes de finalizar.");
        return;
    }

    if (confirm("¿Confirmar pedido y realizar pago?")) {
        alert("¡Compra realizada con éxito! Recibirás un correo de confirmación.");
        localStorage.removeItem('carrito');
        window.location.href = "index.php"; // Redirigir a inicio
    }
}