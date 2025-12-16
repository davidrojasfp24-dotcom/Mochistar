<section>
    <div>
        <div>
            <?php 
            if (isset($error) && $error): 
            ?>
                <div>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=usuario&action=iniciarSesion" method="post">
                
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required> 
                
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</section>