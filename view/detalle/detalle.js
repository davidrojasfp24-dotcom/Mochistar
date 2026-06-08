/**
 * detalle.js
 * Maneja los controles de la página de detalle del producto.
 */

document.addEventListener('DOMContentLoaded', () => {
    const btnMenos = document.getElementById('btn-menos');
    const btnMas = document.getElementById('btn-mas');
    const cantidadNum = document.getElementById('cantidad-num');
    const btnAgregar = document.getElementById('btn-agregar-carrito');

    if (!btnAgregar) return; // Si no hay botón (por ejemplo, producto agotado), no inicializar

    const maxStock = parseInt(btnAgregar.getAttribute('data-max-stock')) || 1;
    let cantidad = 1;

    // Reducir cantidad
    btnMenos.addEventListener('click', () => {
        if (cantidad > 1) {
            cantidad--;
            cantidadNum.innerText = cantidad;
        }
    });

    // Aumentar cantidad
    btnMas.addEventListener('click', () => {
        if (cantidad < maxStock) {
            cantidad++;
            cantidadNum.innerText = cantidad;
        } else {
            alert(`Lo sentimos, solo disponemos de ${maxStock} unidades en stock.`);
        }
    });

    // Agregar al carrito
    btnAgregar.addEventListener('click', () => {
        const id = parseInt(btnAgregar.getAttribute('data-id'));
        const nombre = btnAgregar.getAttribute('data-nombre');
        const precio = parseFloat(btnAgregar.getAttribute('data-precio'));
        const imagen = btnAgregar.getAttribute('data-imagen');

        if (id && nombre && precio && imagen) {
            // Llamar al utilitario del carrito global (carrito-utils.js)
            agregarAlCarrito(id, nombre, precio, imagen, cantidad);
        }
    });
});