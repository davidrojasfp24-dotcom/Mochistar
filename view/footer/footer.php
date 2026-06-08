<link rel="stylesheet" href="view/footer/footer.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<div class="footer-wrapper">
    <div class="footer-inner">

        <!-- Columna 1: Marca -->
        <div class="footer-col footer-brand">
            <p class="footer-tagline">El sabor japonés<br>más auténtico de España.</p>
            <div class="footer-social">
                <a href="#" class="footer-social-link" title="Instagram" id="footer-instagram">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" class="footer-social-link" title="TikTok" id="footer-tiktok">
                    <i class="bi bi-tiktok"></i>
                </a>
                <a href="#" class="footer-social-link" title="Facebook" id="footer-facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="footer-social-link" title="Twitter / X" id="footer-twitter">
                    <i class="bi bi-twitter-x"></i>
                </a>
            </div>
        </div>

        <!-- Columna 2: Navegación -->
        <div class="footer-col">
            <h4 class="footer-heading">Explorar</h4>
            <ul class="footer-links">
                <li><a href="index.php?controller=home&action=ver_home" id="footer-link-inicio">Inicio</a></li>
                <li><a href="index.php?controller=producto&action=ver_carta" id="footer-link-carta">Nuestra Carta</a></li>
                <li><a href="index.php?controller=carrito&action=ver_carrito" id="footer-link-carrito">Mi Carrito</a></li>
                <li><a href="index.php?controller=usuario&action=ver_perfil" id="footer-link-perfil">Mi Perfil</a></li>
            </ul>
        </div>

        <!-- Columna 3: Información -->
        <div class="footer-col">
            <h4 class="footer-heading">Información</h4>
            <ul class="footer-links">
                <li><a href="#" id="footer-link-nosotros">Sobre Nosotros</a></li>
                <li><a href="#" id="footer-link-privacidad">Política de Privacidad</a></li>
                <li><a href="#" id="footer-link-cookies">Política de Cookies</a></li>
                <li><a href="#" id="footer-link-aviso">Aviso Legal</a></li>
            </ul>
        </div>

        <!-- Columna 4: Contacto -->
        <div class="footer-col">
            <h4 class="footer-heading">Contacto</h4>
            <ul class="footer-contact-list">
                <li id="footer-contact-address">
                    <i class="bi bi-geo-alt-fill footer-contact-icon"></i>
                    <span>Calle Gran Vía, 12<br>28013 Madrid, España</span>
                </li>
                <li id="footer-contact-phone">
                    <i class="bi bi-telephone-fill footer-contact-icon"></i>
                    <span>+34 910 123 456</span>
                </li>
                <li id="footer-contact-email">
                    <i class="bi bi-envelope-fill footer-contact-icon"></i>
                    <span>hola@mochistar.es</span>
                </li>
                <li id="footer-contact-hours">
                    <i class="bi bi-clock-fill footer-contact-icon"></i>
                    <span>Lun – Dom: 10:00 – 22:00</span>
                </li>
            </ul>
        </div>

    </div>

    <!-- Línea divisoria -->
    <div class="footer-divider"></div>

    <!-- Pie inferior -->
    <div class="footer-bottom">
        <p class="footer-copy">
            &copy; <?php echo date('Y'); ?> <span class="footer-brand-name">Mochistar</span>. Todos los derechos reservados.
        </p>
        <p class="footer-made">
            Hecho con <i class="bi bi-heart-fill footer-heart"></i> y mucho mochi
        </p>
    </div>

</div>
