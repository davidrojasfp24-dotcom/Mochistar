/**
 * carrito-utils.js
 * Funciones globales para la gestión de carrito mediante LocalStorage.
 */

function agregarAlCarrito(id, nombre, precio, imagen, cantidad = 1) {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const index = carrito.findIndex(item => item.id === id);

    if (index !== -1) {
        carrito[index].cantidad += cantidad;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            imagen: imagen,
            cantidad: cantidad
        });
    }

    localStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarContador();
    alert(`¡${cantidad} x ${nombre} añadido al carrito!`);
}

function actualizarContador() {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const totalProductos = carrito.reduce((acc, item) => acc + item.cantidad, 0);
    
    // Buscar el contador del carrito tanto en escritorio como en móvil si lo hubiera
    const badges = document.querySelectorAll('.cart-count');
    badges.forEach(badge => {
        badge.innerText = totalProductos;
    });
}

// Inicializar contador al cargar la página
document.addEventListener('DOMContentLoaded', actualizarContador);