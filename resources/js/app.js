import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('#sidebar');
    const scrim = document.querySelector('.sidebar-scrim');
    const openButton = document.querySelector('[data-menu-toggle]');
    const closeButton = document.querySelector('[data-menu-close]');

    const closeMenu = () => {
        sidebar?.classList.remove('is-open');
        scrim?.classList.remove('is-visible');
    };

    openButton?.addEventListener('click', () => {
        sidebar?.classList.toggle('is-open');
        scrim?.classList.toggle('is-visible');
    });
    closeButton?.addEventListener('click', closeMenu);
    document.querySelectorAll('.side-nav a').forEach((link) => link.addEventListener('click', closeMenu));
});
