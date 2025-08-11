function toggleDarkMode() {
  const html = document.documentElement;
  const currentTheme = html.getAttribute("data-bs-theme");
  const icon = document.getElementById("darkModeIcon");
  icon.className = currentTheme === "dark" ? "bi bi-brightness-high" : "bi bi-moon";
  if (currentTheme === "dark") {
      html.setAttribute("data-bs-theme", "light");
      localStorage.setItem("theme", "light");
  } else {
      html.setAttribute("data-bs-theme", "dark");
      localStorage.setItem("theme", "dark");
  }
}

window.onload = function () {
  const savedTheme = localStorage.getItem("theme") || "light";
  document.documentElement.setAttribute("data-bs-theme", savedTheme);
}

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
tooltipTriggerList.forEach(el => {
    new bootstrap.Tooltip(el)
})

