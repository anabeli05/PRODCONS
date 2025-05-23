//Revisar y eliminar codigo repetido o inservible en este archivo
document.getElementById('add-editor-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const name = document.getElementById('editor-name').value.trim();
    const role = document.getElementById('editor-role').value.trim();
    const email = document.getElementById('editor-email').value.trim();
    const messageEl = document.getElementById('form-message');

    if (name && role && email) {
        messageEl.textContent = "Administrador agregado exitosamente ✅";
        messageEl.style.color = "green";
        this.reset();
    } else {
        messageEl.textContent = "Por favor, complete todos los campos.";
        messageEl.style.color = "red";
    }

    // Ocultar mensaje después de 3 segundos
    setTimeout(() => {
        messageEl.textContent = "";
    }, 3000);
});
// Función para eliminar un administrador
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-btn')) {
        const confirmDelete = confirm("¿Estás seguro de que deseas eliminar este administrador?");
        if (confirmDelete) {
            const card = e.target.closest('.editor-card');
            card.remove();
        }
    }
});
/* Función para editar un administrador
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('edit-btn')) {
        const card = e.target.closest('.editor-card');
        const name = card.querySelector('.editor-name').textContent;
        const role = card.querySelector('.editor-role').textContent;
        const email = card.querySelector('.editor-email').textContent;

        document.getElementById('editor-name').value = name;
        document.getElementById('editor-role').value = role;
        document.getElementById('editor-email').value = email;

    }
});


//Revisar si se queda o se va
const editBtn = document.getElementById("editBtn");
    const popover = document.getElementById("popover");

    const nameInput = document.getElementById("name");
    const roleInput = document.getElementById("role");
    const emailInput = document.getElementById("email");

    const displayName = document.getElementById("displayName");
    const displayRole = document.getElementById("displayRole");
    const displayEmail = document.getElementById("displayEmail");

    // Mostrar/ocultar menú flotante
    editBtn.addEventListener("click", () => {
      popover.style.display = (popover.style.display === "block") ? "none" : "block";

      // Prellenar con datos actuales
      nameInput.value = displayName.textContent;
      roleInput.value = displayRole.textContent;
      emailInput.value = displayEmail.textContent;
    });

    // Guardar cambios
    document.getElementById("saveBtn").addEventListener("click", () => {
      displayName.textContent = nameInput.value;
      displayRole.textContent = roleInput.value;
      displayEmail.textContent = emailInput.value;

      popover.style.display = "none";
    });

    // Cerrar si hace clic fuera del menú
    document.addEventListener("click", function(event) {
      if (!popover.contains(event.target) && !editBtn.contains(event.target)) {
        popover.style.display = "none";
      }
    });*/
   document.addEventListener('DOMContentLoaded', () => {
    const popover = document.getElementById('popover');
    const nameInput = document.getElementById('name');
    const roleInput = document.getElementById('role');
    const emailInput = document.getElementById('email');
    const saveBtn = document.getElementById('saveBtn');

    let currentCard = null;

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            currentCard = e.target.closest('.editor-card');

            const name = currentCard.querySelector('.editor-name').innerText;
            const role = currentCard.querySelector('.editor-role').innerText.replace('Rol: ', '');
            const email = currentCard.querySelector('.editor-email').innerText.replace('Email: ', '');

            nameInput.value = name;
            roleInput.value = role;
            emailInput.value = email;

            // Posicionar el popover centrado debajo del botón
            const rect = e.target.getBoundingClientRect();
            const popoverWidth = popover.offsetWidth || 260; // valor por defecto si aún no se ha mostrado
            const left = rect.left + rect.width / 2 - popoverWidth / 2;
            const top = rect.bottom + window.scrollY + 8; // pequeño espacio debajo del botón

            popover.style.left = `${left}px`;
            popover.style.top = `${top}px`;
            popover.style.display = 'block';
        });
    });

    saveBtn.addEventListener('click', () => {
        if (currentCard) {
            currentCard.querySelector('.editor-name').innerText = nameInput.value;
            currentCard.querySelector('.editor-role').innerText = 'Rol: ' + roleInput.value;
            currentCard.querySelector('.editor-email').innerText = 'Email: ' + emailInput.value;

            popover.style.display = 'none';
            currentCard = null;
        }
    });
});
// Cerrar popover al hacer clic fuera
document.addEventListener('click', (e) => {
    const isClickInsidePopover = popover.contains(e.target);
    const isEditButton = e.target.classList.contains('edit-btn');

    if (!isClickInsidePopover && !isEditButton) {
        popover.style.display = 'none';
        currentCard = null;
    }
});

// Cerrar popover al presionar Esc
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        popover.style.display = 'none';
        currentCard = null;
    }
});
const userIcon = document.getElementById('user-icon');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('close-btn');
const logoutBtn = document.getElementById('logout-btn');
const overlay = document.getElementById('overlay');
const logo = document.querySelector('.logo');  // Seleccionamos el logo con la clase '.logo'

// Mostrar barra lateral al hacer clic en el ícono del usuario
userIcon.addEventListener('click', () => {
  sidebar.classList.add('visible');
  overlay.classList.remove('hidden');
  sidebar.setAttribute('aria-hidden', 'false');
  
  // Mover el logo y el ícono de usuario hacia la derecha fuera de la vista
  /*logo.classList.add('hide-logo-user');
  userIcon.classList.add('hide-logo-user');*/
});

// Cerrar la barra lateral al hacer clic en el botón de cierre
closeBtn.addEventListener('click', () => {
  sidebar.classList.remove('visible');
  overlay.classList.add('hidden');
  sidebar.setAttribute('aria-hidden', 'true');
  
  // Restaurar la posición original del logo y el ícono de usuario
  /*logo.classList.remove('hide-logo-user');
  userIcon.classList.remove('hide-logo-user');*/
});

// Cerrar la barra lateral al hacer clic en el overlay (área fuera de la barra lateral)
overlay.addEventListener('click', () => {
  sidebar.classList.remove('visible');
  overlay.classList.add('hidden');
  sidebar.setAttribute('aria-hidden', 'true');
  
  // Restaurar la posición original del logo y el ícono de usuario
  logo.classList.remove('hide-logo-user');
  userIcon.classList.remove('hide-logo-user');
});

// Redirigir al hacer clic en "Cerrar sesión"
logoutBtn.addEventListener('click', () => {
  window.location.href = '/login'; // Redirige a la página de login
});