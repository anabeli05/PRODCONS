<<<<<<< HEAD
<button type="submit" class="btn btn-primary">Actualizar Perfil</button>
</form>

<div class="mt-4">
    <a href="cambiar_contraseña.php" class="btn btn-warning">Cambiar Contraseña</a>
</div>

<div class="mt-4">
    <button id="cancelarSuscripcion" class="btn btn-danger">Cancelar Suscripción</button>
</div>

<script>
    document.getElementById('cancelarSuscripcion').addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas cancelar tu suscripción? Se enviará una notificación al SuperAdmin para su aprobación.')) {
            // Enviar mensaje al SuperAdmin
            fetch('notificar_superadmin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mensaje: 'El Editor ha solicitado cancelar su suscripción. Por favor, revisa y aprueba o rechaza la solicitud.'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Se ha enviado una solicitud al SuperAdmin para cancelar tu suscripción.');
                } else {
                    alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');
            });
        }
    });
=======
<button type="submit" class="btn btn-primary">Actualizar Perfil</button>
</form>

<div class="mt-4">
    <a href="cambiar_contraseña.php" class="btn btn-warning">Cambiar Contraseña</a>
</div>

<div class="mt-4">
    <button id="cancelarSuscripcion" class="btn btn-danger">Cancelar Suscripción</button>
</div>

<script>
    document.getElementById('cancelarSuscripcion').addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas cancelar tu suscripción? Se enviará una notificación al SuperAdmin para su aprobación.')) {
            // Enviar mensaje al SuperAdmin
            fetch('notificar_superadmin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mensaje: 'El Editor ha solicitado cancelar su suscripción. Por favor, revisa y aprueba o rechaza la solicitud.'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Se ha enviado una solicitud al SuperAdmin para cancelar tu suscripción.');
                } else {
                    alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');
            });
        }
    });
>>>>>>> main
</script> 