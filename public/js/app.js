document.addEventListener('DOMContentLoaded', () => {

  const openBtn = document.querySelector('[data-login-open]');
  const drawer = document.querySelector('[data-login-drawer]');
  const overlay = document.querySelector('[data-login-overlay]');
  const closeBtn = document.querySelector('[data-login-close]');
  const togglePwd = document.querySelector('[data-toggle-password]');

  if (!openBtn || !drawer || !overlay || !closeBtn) {
    console.log("Login elements not found");
    return;
  }

  function openDrawer(e) {
    e.preventDefault();
    overlay.hidden = false;
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    overlay.hidden = true;
    document.body.style.overflow = '';
  }

  openBtn.addEventListener('click', openDrawer);
  overlay.addEventListener('click', closeDrawer);
  closeBtn.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDrawer();
  });

  if (togglePwd) {
    togglePwd.addEventListener('click', () => {
      const input = togglePwd.closest('.login-password').querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  }

});