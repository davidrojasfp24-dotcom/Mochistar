async function cargarSeccionLogs() {
    const respuesta = await fetch('api/apiLog.php');
    const logs = await respuesta.json();
    
    // Aquí recorres 'logs' y generas las filas de tu tabla HTML
    console.log(logs); 
}