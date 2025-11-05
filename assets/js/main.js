// Ambil tombol hamburger dan menu navigasi
const navToggle = document.querySelector('.mobile-nav-toggle');
const primaryNav = document.querySelector('#primary-navigation');

// Tambahkan event listener ke tombol hamburger
navToggle.addEventListener('click', () => {
    // Cek apakah menu sedang terlihat
    const isVisible = primaryNav.getAttribute('data-visible');

    if (isVisible === 'false') {
        // Jika tersembunyi, tampilkan
        primaryNav.setAttribute('data-visible', 'true');
        navToggle.setAttribute('aria-expanded', 'true');
    } else {
        // Jika terlihat, sembunyikan
        primaryNav.setAttribute('data-visible', 'false');
        navToggle.setAttribute('aria-expanded', 'false');
    }
});