<link rel="stylesheet" href="view/login/login.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<section>
    <div>
        <div>
            <form action="index.php?controller=usuario&action=registrar" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="telefono">Teléfono (opcional):</label>
                <input type="text" id="telefono" name="telefono">
                
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>

                <button type="submit">Registrarse</button>
            </form>
        </div>
    </div>
</section>