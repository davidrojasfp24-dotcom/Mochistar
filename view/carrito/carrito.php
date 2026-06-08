<link rel="stylesheet" href="view/carrito/carrito.css">

<div class="cart-main-container">
    <div class="container py-5">
        <h1 class="page-title">Mi Carrito</h1>
        <div class="row g-5">
            <div class="col-lg-7">
                <div id="lista-productos">
                    </div>

                <div class="payment-method-section mt-5">
                    <div class="last-card-box">
                        <p>Tarjeta de la última compra</p>
                    </div>
                    <button class="btn-cambiar-metodo mt-3">Cambiar método</button>
                </div>
            </div>

            <div class="col-lg-5 text-end summary-section">
                <div id="resumen-totales">
                    </div>
                
                <button class="btn-finalizar-compra mt-4" onclick="finalizarCompra()">
                    Comprar
                </button>
            </div>
        </div>
    </div>
</div>
<script src="view/carrito/carrito.js?v=<?php echo time(); ?>"></script>
