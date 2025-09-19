document.addEventListener("DOMContentLoaded", () => {
  // Usa il tema salvato dall'utente, altrimenti quello dal DB
  const savedTheme = localStorage.getItem('selectedTheme') || fallbackTheme;
  applyTheme(savedTheme);

  // Ascolta i click per il cambio tema
  document.querySelectorAll('[data-theme]').forEach(item => {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      const theme = this.getAttribute('data-theme');
      localStorage.setItem('selectedTheme', theme); // Salva la preferenza
      applyTheme(theme);
    });
  });
});

function applyTheme(theme) {
  let themeLink = document.getElementById('theme-css');

  if (!themeLink) {
    themeLink = document.createElement('link');
    themeLink.id = 'theme-css';
    themeLink.rel = 'stylesheet';
    document.head.appendChild(themeLink);
  }

  if (theme === 'default') {
    themeLink.href = '';
    localStorage.removeItem('selectedTheme');
    document.body.style.visibility = 'visible';
  } else {
    themeLink.href = `${themeBasePath}tema-${theme}.css`;

    themeLink.onload = () => {
      document.body.style.visibility = 'visible';
    };

    themeLink.onerror = () => {
      console.warn("Tema non caricato, ripristino tema default.");
      document.body.style.visibility = 'visible';
      localStorage.removeItem('selectedTheme');
    };
  }
}
