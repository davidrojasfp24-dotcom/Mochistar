//Cuando la página termina de cargar, arrancamos el sistema para mostrar los datos guardados
document.addEventListener('DOMContentLoaded', () => {
    actualizarInterfaz();
});

//Esta función es como el director de orquesta: decide qué partes de la web hay que pintar de nuevo
function actualizarInterfaz() {
    //Refresca los botones del catálogo (por si estamos en la tienda)
    actualizarBotonesCatalogo();

    //Si en la página actual existe el contenedor de la lista, es que estamos dentro del carrito
    if (document.getElementById('lista-productos')) {
        renderizarCarrito();
    }
}

// --- LÓGICA PARA EL CATÁLOGO (Botones inteligentes) ---

function actualizarBotonesCatalogo() {
    //Sacamos los productos guardados en la memoria del navegador (LocalStorage)
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    
    //Buscamos todos los huecos que el PHP dejó preparados para los botones
    const contenedores = document.querySelectorAll('[id^="contenedor-boton-"]');
    
    contenedores.forEach(div => {
        //Sacamos el ID del producto directamente del nombre del ID del div
        const idProducto = parseInt(div.id.replace('contenedor-boton-', ''));
        //Comprobamos si este mochi ya está en nuestro carrito
        const item = carrito.find(p => p.id === idProducto);
        
        if (item) {
            //Si ya está en el carrito, transformamos el botón en un selector de cantidad (+ / -)
            div.innerHTML = `
                <div class="selector-cantidad">
                    <button class="btn-flecha" onclick="cambiarCantidadGlobal(${idProducto}, -1)">-</button>
                    <span class="fw-bold">${item.cantidad}</span>
                    <button class="btn-flecha" onclick="cambiarCantidadGlobal(${idProducto}, 1)">+</button>
                </div>`;
        } else {
            //Si no está, mostramos el botón azul de "COMPRAR" original
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
    
    //Leemos los datos que el PHP escribió en los atributos 'data-' (nombre, precio, imagen)
    const nuevoProducto = {
        id: id,
        nombre: contenedor.getAttribute('data-nombre'),
        precio: parseFloat(contenedor.getAttribute('data-precio')),
        image: contenedor.getAttribute('data-image'),
        cantidad: 1
    };

    //Cogemos lo que ya había en el carrito, añadimos el nuevo mochi y lo volvemos a guardar
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    carrito.push(nuevoProducto);
    localStorage.setItem('carrito', JSON.stringify(carrito));
    
    //Refrescamos la interfaz para que el botón cambie al instante
    actualizarInterfaz();
}

//Esta función sirve para todo: subir cantidad, bajarla o borrar el producto si llega a cero
function cambiarCantidadGlobal(id, cambio) {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const index = carrito.findIndex(p => p.id === id);

    if (index !== -1) {
        //Aplicamos el cambio (+1 o -1)
        carrito[index].cantidad += cambio;
        
        //Si la cantidad es 0 o menos, el usuario quiere quitar el producto del carrito
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
        }
        
        //Guardamos los cambios en la memoria del navegador
        localStorage.setItem('carrito', JSON.stringify(carrito));
        actualizarInterfaz();
    }
}

// --- LÓGICA PARA LA PÁGINA DEL CARRITO (La lista detallada) ---

function renderizarCarrito() {
    const lista = document.getElementById('lista-productos');
    const resumen = document.getElementById('resumen-totales');
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];

    //Si el carrito está vacío, mostramos un mensaje amigable
    if (carrito.length === 0) {
        lista.innerHTML = '<h4 class="text-white opacity-50">No hay productos en tu carrito.</h4>';
        resumen.innerHTML = '<p class="text-white">Subtotal: 0,00€</p>';
        return;
    }

    let htmlContenido = '';
    let subtotal = 0;

    //Generamos el HTML para cada producto que haya en el carrito
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

    //Calculamos el IVA (10%) y el total final
    const impuesto = subtotal * 0.10;
    const totalFinal = subtotal + impuesto;

    //Pintamos los resultados en la página
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

// --- FINALIZAR LA COMPRA ---

function finalizarCompra() {
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    
    if (carrito.length === 0) {
        alert("Añade algún producto antes de finalizar.");
        return;
    }

    //Pedimos confirmación al usuario antes de borrar los datos
    if (confirm("¿Confirmar pedido y realizar pago?")) {
        alert("¡Compra realizada con éxito! Recibirás un correo de confirmación.");
        //Limpiamos el carrito de la memoria para que la siguiente compra empiece de cero
        localStorage.removeItem('carrito');
        //Redirigimos al inicio
        window.location.href = "index.php"; 
    }
}