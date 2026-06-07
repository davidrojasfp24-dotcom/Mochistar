<link rel="stylesheet" href="view/login/login.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<section>
    <div>
        <div class="login-card">
            <div id="error-container"></div>

            <form id="loginForm" action="index.php?controller=usuario&action=iniciarSesion" method="post">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo isset($_COOKIE['recordar_email']) ? htmlspecialchars($_COOKIE['recordar_email']) : ''; ?>" required> 
                
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
                
                <div class="remember-container">
                    <input type="checkbox" id="recordar" name="recordar" <?php echo isset($_COOKIE['recordar_email']) ? 'checked' : ''; ?>>
                    <label for="recordar" class="remember-label">Recordar mi email</label>
                </div>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</section>

<?php if (isset($error)): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mensaje = "<?= htmlspecialchars($error) ?>";
        if (typeof mostrarError === 'function') {
            mostrarError(mensaje);
        } else {
            const container = document.getElementById('error-container');
            container.innerHTML = `<div class="error-box"><i class="bi bi-exclamation-triangle-fill me-2"></i>${mensaje}</div>`;
        }
    });
</script>
<?php endif; ?>

<script src="view/login/login.js"></script>