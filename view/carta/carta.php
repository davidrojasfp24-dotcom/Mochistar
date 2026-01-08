<link rel="stylesheet" href="view/carta/carta.css">

<div class="container my-5">
    <div class="row g-4">
        <?php foreach ($productos as $producto): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="mochi-card">
                    <div class="mochi-img-container">
                        <img src="view/home/img/<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>">
                    </div>

                    <div class="mochi-body">
                        <h5 class="mochi-title"><?= $producto['nombre'] ?></h5>
                        <p class="mochi-description">
                            <?= strip_tags($producto['descripcion']) ?>
                        </p>


                        <a href="index.php?controller=producto&action=ver_detalle&id=<?= $producto['id_producto'] ?>" class="mochi-link">Ver más...</a>
                        
                        <div class="mochi-footer">
                            <span class="mochi-price"><?= number_format($producto['precio_unidad'], 2, ',', '.') ?>€</span>
                            <button class="btn-comprar" 
                                onclick="agregarAlCarrito(<?= $producto['id_producto'] ?>, '<?= $producto['nombre'] ?>', <?= $producto['precio_unidad'] ?>, '<?= $producto['imagen'] ?>')">
                                COMPRAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="view/carta/carta.js"></script>