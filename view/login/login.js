// 1. Función Global para mostrar errores
function mostrarError(mensaje) {
    const container = document.getElementById('error-container');
    if (!container) return;

    container.innerHTML = `
        <div class="error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>${mensaje}</span>
        </div>
    `;
}

//Cargar eventos al DOM
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const container = document.getElementById('error-container');

    if (loginForm) {
        //Validacion para ver que no este vacio
        loginForm.addEventListener('submit', (e) => {
            const email = document.getElementById('email').value.trim();
            const pass = document.getElementById('contrasena').value.trim();

            if (email === "" || pass === "") {
                e.preventDefault();
                mostrarError("Por favor, rellena todos los campos.");
            }
        });

        //Limpiar si no esta bien
        loginForm.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                container.innerHTML = '';
            });
        });
    }
});