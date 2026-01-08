<link rel="stylesheet" href="view/home/homeStyles.css">

<div class="container my-5">
    <div class="row g-4">
        <?php foreach ($productos as $producto): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="mochi-card">
                    <div class="mochi-img-container">
                        <img src="assets/img/<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>">
                    </div>

                    <div class="mochi-body">
                        <h5 class="mochi-title"><?= $producto['nombre'] ?></h5>
                        <a href="index.php?controller=producto&action=ver_detalle&id=<?= $producto['id'] ?>" class="mochi-link">Ver más...</a>
                        
                        <div class="mochi-footer">
                            <span class="mochi-price"><?= number_format($producto['precio'], 2, ',', '.') ?>€</span>
                            <button class="btn-comprar" onclick="comprar(<?= $producto['id'] ?>)">
                                COMPRAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>