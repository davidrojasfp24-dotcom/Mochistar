<?php
// Seguridad: si no hay sesión iniciada, redirigir al login
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?controller=usuario&action=ver_login');
    exit();
}

$usuario = $_SESSION['usuario'];

// Obtener pedidos del usuario
include_once 'model/pedidoDAO.php';
$todosPedidos = pedidoDAO::getPedidos();
$misPedidos = array_filter($todosPedidos, function($p) use ($usuario) {
    return $p['id_usuario'] == $usuario->getId();
});
$misPedidos = array_values($misPedidos); // reindexar
?>

<link rel="stylesheet" href="view/perfil/perfil.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<div class="perfil-page">

    <!-- ═══════════════════════════════════════════
         CABECERA DEL PERFIL
    ═══════════════════════════════════════════ -->
    <div class="perfil-hero">
        <div class="perfil-hero-bg"></div>
        <div class="perfil-hero-content">

            <!-- Avatar -->
            <div class="perfil-avatar-wrapper">
                <div class="perfil-avatar" id="perfil-avatar">
                    <span class="perfil-avatar-initial">
                        <?php echo strtoupper(substr($usuario->getNombre(), 0, 1)); ?>
                    </span>
                    <div class="perfil-avatar-ring"></div>
                </div>
                <div class="perfil-badge-rol">
                    <i class="bi bi-<?php echo $usuario->getRol() === 'admin' ? 'shield-fill-check' : 'person-fill'; ?>"></i>
                    <?php echo ucfirst($usuario->getRol()); ?>
                </div>
            </div>

            <!-- Nombre y datos rápidos -->
            <div class="perfil-hero-info">
                <h1 class="perfil-nombre" id="perfil-nombre">
                    <?php echo htmlspecialchars($usuario->getNombre() . ' ' . $usuario->getApellido()); ?>
                </h1>
                <p class="perfil-email-hero" id="perfil-email-hero">
                    <i class="bi bi-envelope-fill"></i>
                    <?php echo htmlspecialchars($usuario->getEmail()); ?>
                </p>
                <div class="perfil-stats">
                    <div class="perfil-stat-item">
                        <span class="perfil-stat-num"><?php echo count($misPedidos); ?></span>
                        <span class="perfil-stat-label">Pedidos</span>
                    </div>
                    <div class="perfil-stat-divider"></div>
                    <div class="perfil-stat-item">
                        <span class="perfil-stat-num">
                            <?php
                                $gastado = array_sum(array_column($misPedidos, 'precio'));
                                echo number_format($gastado, 2, ',', '.') . '€';
                            ?>
                        </span>
                        <span class="perfil-stat-label">Total gastado</span>
                    </div>
                    <div class="perfil-stat-divider"></div>
                    <div class="perfil-stat-item">
                        <span class="perfil-stat-num">
                            <?php
                                $pendientes = count(array_filter($misPedidos, fn($p) => $p['estado'] === 'Pendiente'));
                                echo $pendientes;
                            ?>
                        </span>
                        <span class="perfil-stat-label">Pendientes</span>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="perfil-hero-actions">
                <a href="index.php?controller=usuario&action=logout" class="btn-perfil-logout" id="perfil-btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </a>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════
         CUERPO: 2 COLUMNAS
    ═══════════════════════════════════════════ -->
    <div class="perfil-body">

        <!-- ── COLUMNA IZQUIERDA: Datos personales ── -->
        <aside class="perfil-sidebar">

            <div class="perfil-card" id="perfil-card-datos">
                <div class="perfil-card-header">
                    <i class="bi bi-person-lines-fill"></i>
                    <h2>Datos personales</h2>
                </div>
                <ul class="perfil-datos-list">
                    <li class="perfil-dato-item">
                        <span class="perfil-dato-icon"><i class="bi bi-person-fill"></i></span>
                        <div class="perfil-dato-info">
                            <span class="perfil-dato-label">Nombre</span>
                            <span class="perfil-dato-val" id="dato-nombre">
                                <?php echo htmlspecialchars($usuario->getNombre()); ?>
                            </span>
                        </div>
                    </li>
                    <li class="perfil-dato-item">
                        <span class="perfil-dato-icon"><i class="bi bi-person-badge-fill"></i></span>
                        <div class="perfil-dato-info">
                            <span class="perfil-dato-label">Apellido</span>
                            <span class="perfil-dato-val" id="dato-apellido">
                                <?php echo htmlspecialchars($usuario->getApellido()); ?>
                            </span>
                        </div>
                    </li>
                    <li class="perfil-dato-item">
                        <span class="perfil-dato-icon"><i class="bi bi-envelope-fill"></i></span>
                        <div class="perfil-dato-info">
                            <span class="perfil-dato-label">Email</span>
                            <span class="perfil-dato-val" id="dato-email">
                                <?php echo htmlspecialchars($usuario->getEmail()); ?>
                            </span>
                        </div>
                    </li>
                    <li class="perfil-dato-item">
                        <span class="perfil-dato-icon"><i class="bi bi-telephone-fill"></i></span>
                        <div class="perfil-dato-info">
                            <span class="perfil-dato-label">Teléfono</span>
                            <span class="perfil-dato-val" id="dato-telefono">
                                <?php echo htmlspecialchars($usuario->getTelefono() ?: '—'); ?>
                            </span>
                        </div>
                    </li>
                    <li class="perfil-dato-item">
                        <span class="perfil-dato-icon"><i class="bi bi-shield-fill-check"></i></span>
                        <div class="perfil-dato-info">
                            <span class="perfil-dato-label">Rol</span>
                            <span class="perfil-dato-val perfil-rol-badge perfil-rol-<?php echo $usuario->getRol(); ?>" id="dato-rol">
                                <?php echo ucfirst($usuario->getRol()); ?>
                            </span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Tarjeta de ayuda rápida -->
            <div class="perfil-card perfil-card-help" id="perfil-card-help">
                <div class="perfil-card-header">
                    <i class="bi bi-question-circle-fill"></i>
                    <h2>¿Necesitas ayuda?</h2>
                </div>
                <p class="perfil-help-text">Nuestro equipo está disponible de <strong>10:00 a 22:00</strong> todos los días.</p>
                <a href="mailto:hola@mochistar.es" class="btn-perfil-help" id="perfil-btn-help">
                    <i class="bi bi-envelope"></i> Contactar soporte
                </a>
            </div>

        </aside>

        <!-- ── COLUMNA DERECHA: Historial de pedidos ── -->
        <main class="perfil-main">

            <div class="perfil-card perfil-card-pedidos" id="perfil-card-pedidos">
                <div class="perfil-card-header">
                    <i class="bi bi-bag-heart-fill"></i>
                    <h2>Mis pedidos</h2>
                    <span class="perfil-pedidos-count"><?php echo count($misPedidos); ?></span>
                </div>

                <?php if (empty($misPedidos)): ?>
                    <!-- Estado vacío -->
                    <div class="perfil-empty-state" id="perfil-empty-state">
                        <div class="perfil-empty-icon">
                            <i class="bi bi-bag-x"></i>
                        </div>
                        <h3>Aún no tienes pedidos</h3>
                        <p>¡Prueba nuestros mochis! Seguro que repites 😊</p>
                        <a href="index.php?controller=producto&action=ver_carta" class="btn-perfil-cta" id="perfil-btn-carta">
                            Ver la carta
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Lista de pedidos -->
                    <div class="perfil-pedidos-list" id="perfil-pedidos-list">
                        <?php foreach ($misPedidos as $index => $pedido): ?>
                            <div class="perfil-pedido-item" id="pedido-item-<?php echo $pedido['id_pedido']; ?>" style="animation-delay: <?php echo $index * 0.07; ?>s">
                                
                                <!-- Número e icono -->
                                <div class="pedido-num-col">
                                    <div class="pedido-icon-circle">
                                        <i class="bi bi-bag-fill"></i>
                                    </div>
                                    <span class="pedido-num">#<?php echo str_pad($pedido['id_pedido'], 4, '0', STR_PAD_LEFT); ?></span>
                                </div>

                                <!-- Info central -->
                                <div class="pedido-info-col">
                                    <span class="pedido-fecha">
                                        <i class="bi bi-calendar3"></i>
                                        <?php echo date('d/m/Y · H:i', strtotime($pedido['fecha'])); ?>
                                    </span>
                                </div>

                                <!-- Precio -->
                                <div class="pedido-precio-col">
                                    <span class="pedido-precio">
                                        <?php echo number_format($pedido['precio'], 2, ',', '.'); ?>€
                                    </span>
                                </div>

                                <!-- Estado -->
                                <div class="pedido-estado-col">
                                    <?php
                                        $estado = $pedido['estado'];
                                        $estadoClass = match(strtolower($estado)) {
                                            'pendiente'  => 'estado-pendiente',
                                            'enviado'    => 'estado-enviado',
                                            'entregado'  => 'estado-entregado',
                                            'cancelado'  => 'estado-cancelado',
                                            default      => 'estado-pendiente'
                                        };
                                        $estadoIcon = match(strtolower($estado)) {
                                            'pendiente'  => 'clock-fill',
                                            'enviado'    => 'truck',
                                            'entregado'  => 'check-circle-fill',
                                            'cancelado'  => 'x-circle-fill',
                                            default      => 'clock-fill'
                                        };
                                    ?>
                                    <span class="pedido-estado-badge <?php echo $estadoClass; ?>" id="estado-pedido-<?php echo $pedido['id_pedido']; ?>">
                                        <i class="bi bi-<?php echo $estadoIcon; ?>"></i>
                                        <?php echo htmlspecialchars($estado); ?>
                                    </span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>

    </div>
</div>
