function agregarAlCarrito(id, nombre, precio, imagen) {
    // 1. Obtener el carrito actual de LocalStorage (si no existe, crear un array vacío)
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

    // 2. Comprobar si el producto ya está en el carrito
    const index = carrito.findIndex(item => item.id === id);

    if (index !== -1) {
        // Si ya existe, aumentamos la cantidad
        carrito[index].cantidad += 1;
    } else {
        // Si es nuevo, lo añadimos al array
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            imagen: imagen,
            cantidad: 1
        });
    }

    // 3. Guardar el carrito actualizado en LocalStorage (convertido a texto)
    localStorage.setItem('carrito', JSON.stringify(carrito));

    // 4. Feedback visual (opcional)
    alert(`¡${nombre} añadido al carrito!`);
    console.log(carrito);
}

function actualizarContador() {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    // Sumamos todas las cantidades de los productos
    const totalProductos = carrito.reduce((acc, item) => acc + item.cantidad, 0);
    
    // Suponiendo que tienes un elemento con id="cart-count"
    const badge = document.getElementById('cart-count');
    if(badge) badge.innerText = totalProductos;
}

// Ejecutar al cargar la página para que el número no desaparezca al refrescar
document.addEventListener('DOMContentLoaded', actualizarContador);