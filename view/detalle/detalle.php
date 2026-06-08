<?php
// Seguridad: $producto y $relacionados vienen del productoController::ver_detalle()
if (!isset($producto)) {
    header("Location: index.php?controller=producto&action=ver_carta");
    exit();
}
?>
<link rel="stylesheet" href="view/detalle/detalle.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<div class="detalle-wrapper">
    <div class="container">

        <!-- BREADCRUMB (MIGAS DE PAN) -->
        <nav class="detalle-breadcrumb" aria-label="breadcrumb">
            <a href="index.php?controller=home&action=ver_home"><i class="bi bi-house-fill"></i> Inicio</a>
            <span class="separator">/</span>
            <a href="index.php?controller=producto&action=ver_carta">Carta</a>
            <span class="separator">/</span>
            <span class="current"><?php echo htmlspecialchars($producto['nombre']); ?></span>
        </nav>

        <!-- CONTENEDOR PRINCIPAL DEL DETALLE -->
        <div class="detalle-main">

            <!-- LADO IZQUIERDO: IMAGEN DEL MOCHI -->
            <div class="detalle-img-wrap">
                <img src="view/home/img/<?php echo htmlspecialchars($producto['imagen'] ?? 'default.png'); ?>"
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                     id="detalle-imagen">

                <?php $stock = intval($producto['cantidad']); ?>
                <span class="detalle-badge-stock <?php echo $stock > 0 ? 'badge-disponible' : 'badge-agotado'; ?>">
                    <i class="bi bi-<?php echo $stock > 0 ? 'check-circle' : 'x-circle'; ?> me-1"></i>
                    <?php echo $stock > 0 ? 'Disponible' : 'Agotado'; ?>
                </span>
            </div>

            <!-- LADO DERECHO: INFORMACIÓN Y ACCIONES -->
            <div class="detalle-info">
                <p class="detalle-categoria">
                    <i class="bi bi-tag-fill me-1"></i> Mochi Artesanal Japonés
                </p>

                <h1 class="detalle-nombre" id="detalle-nombre">
                    <?php echo htmlspecialchars($producto['nombre']); ?>
                </h1>

                <p class="detalle-descripcion" id="detalle-descripcion">
                    <?php echo nl2br(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción disponible.')); ?>
                </p>

                <div class="detalle-precio-wrap">
                    <span class="detalle-precio" id="detalle-precio">
                        <?php echo number_format($producto['precio_unidad'], 2, ',', '.'); ?>€
                    </span>
                    <span class="detalle-precio-label">IVA incluido</span>
                </div>

                <hr class="detalle-divider">

                <!-- Ficha rápida / Datos del producto -->
                <div class="detalle-quick-info">
                    <div class="detalle-quick-item">
                        <span class="detalle-quick-label">Referencia</span>
                        <span class="detalle-quick-val">#<?php echo str_pad($producto['id_producto'], 4, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="detalle-quick-item">
                        <span class="detalle-quick-label">Stock disponible</span>
                        <span class="detalle-quick-val"><?php echo $stock; ?> unidades</span>
                    </div>
                    <div class="detalle-quick-item">
                        <span class="detalle-quick-label">Origen</span>
                        <span class="detalle-quick-val">100% Artesanal</span>
                    </div>
                </div>

                <hr class="detalle-divider">

                <!-- Selector de cantidad y comprar -->
                <?php if ($stock > 0): ?>
                    <div class="detalle-cantidad-wrap">
                        <span class="detalle-cantidad-label">Selecciona Cantidad</span>
                        <div class="detalle-cantidad-controls">
                            <button class="btn-cantidad" id="btn-menos" aria-label="Reducir cantidad">−</button>
                            <span class="cantidad-num" id="cantidad-num">1</span>
                            <button class="btn-cantidad" id="btn-mas" aria-label="Aumentar cantidad">+</button>
                        </div>
                    </div>

                    <div class="detalle-actions">
                        <button class="btn-detalle-comprar" id="btn-agregar-carrito"
                                data-id="<?php echo $producto['id_producto']; ?>"
                                data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                data-precio="<?php echo $producto['precio_unidad']; ?>"
                                data-imagen="<?php echo htmlspecialchars($producto['imagen']); ?>"
                                data-max-stock="<?php echo $stock; ?>">
                            <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                        </button>
                        <a href="index.php?controller=producto&action=ver_carta" class="btn-detalle-volver">
                            <i class="bi bi-arrow-left"></i> Volver a la Carta
                        </a>
                    </div>
                <?php else: ?>
                    <div class="detalle-actions">
                        <button class="btn-detalle-comprar btn-disabled" disabled>
                            <i class="bi bi-x-circle me-2"></i> Temporalmente Agotado
                        </button>
                        <a href="index.php?controller=producto&action=ver_carta" class="btn-detalle-volver">
                            <i class="bi bi-arrow-left"></i> Volver a la Carta
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- SECCIÓN PRODUCTOS RELACIONADOS -->
        <?php if (!empty($relacionados)): ?>
            <div class="detalle-relacionados">
                <h2 class="detalle-relacionados-titulo">También te puede gustar</h2>
                <div class="relacionados-grid">
                    <?php foreach ($relacionados as $rel): ?>
                        <a href="index.php?controller=producto&action=ver_detalle&id=<?php echo $rel['id_producto']; ?>" class="rel-card">
                            <div class="rel-card-img-wrap">
                                <img src="view/home/img/<?php echo htmlspecialchars($rel['imagen'] ?? 'default.png'); ?>"
                                     alt="<?php echo htmlspecialchars($rel['nombre']); ?>"
                                     class="rel-card-img">
                            </div>
                            <div class="rel-card-body">
                                <span class="rel-card-nombre"><?php echo htmlspecialchars($rel['nombre']); ?></span>
                                <span class="rel-card-precio"><?php echo number_format($rel['precio_unidad'], 2, ',', '.'); ?>€</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="view/detalle/detalle.js?v=<?php echo time(); ?>"></script>