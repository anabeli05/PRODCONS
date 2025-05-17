document.addEventListener("DOMContentLoaded", () => {
  const viewsElement = document.getElementById("views");
  const axis = document.getElementById("axis");

  let totalViews = parseInt(localStorage.getItem("totalViews")) || 0;

  if (!sessionStorage.getItem("viewCounted")) {
    totalViews += 1;
    localStorage.setItem("totalViews", totalViews);
    sessionStorage.setItem("viewCounted", "true");
  }

  viewsElement.textContent = `👁️ ${totalViews.toLocaleString('es-ES')}`;

  const bars = document.querySelectorAll(".bar");
  let maxViews = 0;

  bars.forEach(bar => {
    const views = parseInt(bar.getAttribute("data-views"));
    if (views > maxViews) maxViews = views;
  });

  const roundToMillion = n => Math.ceil(n / 1000000) * 1000000;
  const maxRounded = roundToMillion(maxViews);

  bars.forEach(bar => {
    const views = parseInt(bar.getAttribute("data-views"));
    const percent = (views / maxRounded) * 100;
    let current = 0;
    const step = percent / 60;

    const animate = () => {
      current += step;
      if (current >= percent) {
        bar.style.width = `${percent}%`;
        return;
      }
      bar.style.width = `${current}%`;
      requestAnimationFrame(animate);
    };

    animate();
    bar.setAttribute("data-label", views.toLocaleString('es-ES'));
  });

  axis.innerHTML = "";
  for (let i = 0; i <= 7; i++) {
    const val = Math.round((i / 7) * maxRounded);
    const label = val === 0 ? "0" : `${Math.round(val / 1000000)}M`;
    const span = document.createElement("span");
    span.textContent = label;
    axis.appendChild(span);
  }

  // SIDEBAR FUNCIONALIDAD
  const adminBtn = document.getElementById("admin-btn");
  const sidebar = document.getElementById("sidebar");
  const closeBtn = document.getElementById("close-btn");
  const overlay = document.getElementById("overlay");

  adminBtn.addEventListener("click", () => {
    sidebar.classList.add("visible-admin");
    overlay.classList.remove("hidden-admin");
  });

  closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("visible-admin");
    overlay.classList.add("hidden-admin");
  });

  overlay.addEventListener("click", () => {
    sidebar.classList.remove("visible-admin");
    overlay.classList.add("hidden-admin");
  });
});
