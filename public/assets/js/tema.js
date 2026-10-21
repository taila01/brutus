(() => {
  'use strict';
  if (window.brutusThemeInitialized) return;
  window.brutusThemeInitialized = true;
  const key = 'brutus-theme';
  let saved;
  try { saved = localStorage.getItem(key); } catch (_) { /* Navegação privada também permite alternar. */ }
  function apply(theme) {
    const dark = theme === 'dark';
    document.documentElement.dataset.theme = dark ? 'dark' : 'light';
    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
      const label = dark ? 'Ativar modo claro' : 'Ativar modo noturno';
      button.setAttribute('aria-label', label);
      button.setAttribute('title', label);
      button.setAttribute('aria-pressed', String(dark));
    });
  }
  apply(saved === 'dark' ? 'dark' : 'light');
  document.addEventListener('click', event => {
    if (!event.target.closest('[data-theme-toggle]')) return;
    const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    apply(next);
    try { localStorage.setItem(key, next); } catch (_) { /* O tema permanece ativo nesta página. */ }
  });
  document.addEventListener('DOMContentLoaded', () => apply(document.documentElement.dataset.theme));
  window.addEventListener('storage', event => {
    if (event.key === key || event.key === null) apply(event.newValue === 'dark' ? 'dark' : 'light');
  });
})();
