<div class="mt-4">
    <h3>Solicitudes de Cancelación de Suscripción</h3>
    <div id="solicitudesCancelacion">
        <!-- Aquí se mostrarán las solicitudes de cancelación -->
    </div>
</div>

<div class="mt-4">
    <h3>Solicitudes de Cancelación de Suscripción de Usuarios</h3>
    <div id="solicitudesCancelacionUsuarios">
        <!-- Aquí se mostrarán las solicitudes de cancelación de usuarios -->
    </div>
</div>

<script>
    // Función para cargar las solicitudes de cancelación
    function cargarSolicitudesCancelacion() {
        fetch('obtener_solicitudes_cancelacion.php')
            .then(response => response.json())
            .then(data => {
                const solicitudesDiv = document.getElementById('solicitudesCancelacion');
                solicitudesDiv.innerHTML = '';
                data.forEach(solicitud => {
                    const solicitudElement = document.createElement('div');
                    solicitudElement.className = 'solicitud';
                    solicitudElement.innerHTML = `
                        <p>Editor: ${solicitud.editor}</p>
                        <p>Mensaje: ${solicitud.mensaje}</p>
                        <button onclick="aprobarSolicitud(${solicitud.id})">Aprobar</button>
                        <button onclick="rechazarSolicitud(${solicitud.id})">Rechazar</button>
                    `;
                    solicitudesDiv.appendChild(solicitudElement);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Función para aprobar una solicitud
    function aprobarSolicitud(id) {
        fetch('aprobar_solicitud.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Solicitud aprobada.');
                cargarSolicitudesCancelacion();
            } else {
                alert('Error al aprobar la solicitud.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Función para rechazar una solicitud
    function rechazarSolicitud(id) {
        fetch('rechazar_solicitud.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Solicitud rechazada.');
                cargarSolicitudesCancelacion();
            } else {
                alert('Error al rechazar la solicitud.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Función para cargar las solicitudes de cancelación de usuarios
    function cargarSolicitudesCancelacionUsuarios() {
        fetch('obtener_solicitudes_cancelacion_usuarios.php')
            .then(response => response.json())
            .then(data => {
                const solicitudesDiv = document.getElementById('solicitudesCancelacionUsuarios');
                solicitudesDiv.innerHTML = '';
                data.forEach(solicitud => {
                    const solicitudElement = document.createElement('div');
                    solicitudElement.className = 'solicitud';
                    solicitudElement.innerHTML = `
                        <p>Usuario: ${solicitud.usuario}</p>
                        <p>Mensaje: ${solicitud.mensaje}</p>
                        <button onclick="aprobarSolicitudUsuario(${solicitud.id})">Aprobar</button>
                        <button onclick="rechazarSolicitudUsuario(${solicitud.id})">Rechazar</button>
                    `;
                    solicitudesDiv.appendChild(solicitudElement);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Función para aprobar una solicitud de usuario
    function aprobarSolicitudUsuario(id) {
        fetch('aprobar_solicitud_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Solicitud de usuario aprobada.');
                cargarSolicitudesCancelacionUsuarios();
            } else {
                alert('Error al aprobar la solicitud de usuario.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Función para rechazar una solicitud de usuario
    function rechazarSolicitudUsuario(id) {
        fetch('rechazar_solicitud_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Solicitud de usuario rechazada.');
                cargarSolicitudesCancelacionUsuarios();
            } else {
                alert('Error al rechazar la solicitud de usuario.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Cargar solicitudes al cargar la página
    cargarSolicitudesCancelacion();
    cargarSolicitudesCancelacionUsuarios();
</script> 