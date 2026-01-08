document.addEventListener('DOMContentLoaded', () => {
    renderizarCarrito();
});

function renderizarCarrito() {
    const lista = document.getElementById('lista-productos');
    const resumen = document.getElementById('resumen-totales');
    if (!lista || !resumen) return;

    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    
    if (carrito.length === 0) {
        lista.innerHTML = '<p class="text-white">Tu carrito está vacío.</p>';
        resumen.innerHTML = '<p class="text-white">Subtotal 0,00€</p>';
        return;
    }

    let productosHTML = '';
    let subtotal = 0;

    // Usamos el index para saber exactamente qué producto borrar o editar
    carrito.forEach((item, index) => {
        const precio = parseFloat(item.precio) || 0;
        const cantidad = parseInt(item.cantidad) || 1;
        subtotal += precio * cantidad;

        productosHTML += `
            <div class="cart-item d-flex align-items-center justify-content-between mb-4 text-white">
                <div class="d-flex align-items-center">
                    <img src="assets/img/${item.image}" alt="${item.nombre}" 
                         style="width: 65px; height: 65px; border-radius: 12px; margin-right: 20px; object-fit: cover;">
                    <div>
                        <h5 class="mb-0">${item.nombre}</h5>
                        <small class="text-white-50">${precio.toFixed(2)}€</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="qty-controls d-flex align-items-center me-4">
                        <button class="btn-qty" onclick="cambiarCantidad(${index}, -1)">-</button>
                        <span class="mx-3 fw-bold">${cantidad}</span>
                        <button class="btn-qty" onclick="cambiarCantidad(${index}, 1)">+</button>
                    </div>
                    
                    <button class="btn-remove" onclick="eliminarProducto(${index})">
                         <i class="bi bi-trash"></i> Borrar
                    </button>
                </div>
            </div>`;
    });

    const impuesto = subtotal * 0.10;
    const total = subtotal + impuesto;

    lista.innerHTML = productosHTML;
    resumen.innerHTML = `
        <div class="text-white text-end">
            <p class="mb-1">Subtotal ${subtotal.toFixed(2)}€</p>
            <p class="mb-1">Impuesto 10% ${impuesto.toFixed(2)}€</p>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px; border-top: 1px solid #333; padding-top: 10px;">
                Total ${total.toFixed(2)}€
            </div>
        </div>`;
}

// Función para subir o bajar cantidad
function cambiarCantidad(index, delta) {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    let nuevaCantidad = (parseInt(carrito[index].cantidad) || 1) + delta;

    if (nuevaCantidad > 0) {
        carrito[index].cantidad = nuevaCantidad;
    } else {
        // Si baja de 1, lo eliminamos
        return eliminarProducto(index);
    }

    localStorage.setItem('carrito', JSON.stringify(carrito));
    renderizarCarrito();
}

// Función para eliminar producto
function eliminarProducto(index) {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    carrito.splice(index, 1); // Quita el elemento del array
    localStorage.setItem('carrito', JSON.stringify(carrito));
    renderizarCarrito();
}

function finalizarCompra() {
    alert("¡Pedido realizado con éxito!");
    localStorage.removeItem('carrito');
    location.reload();
}