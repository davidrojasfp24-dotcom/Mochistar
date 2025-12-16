<form action="index.php?controller=usuario&action=registrar" method="POST">
    
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br>
    
    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="telefono">Teléfono (opcional):</label>
    <input type="text" id="telefono" name="telefono"><br>
    
    <label for="contrasena">Contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required><br>

    <button type="submit">Registrarse</button>
</form>